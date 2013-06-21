<? //renew_upgrade_sellers_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Renew_upgrade_sellers_ads extends Site {
	var $classified_user_id;
	var $classified_id;
	var $classified_data;
	var $category_id;
	var $price_plan_id;
	var $price_plan;
	var $user_data;
	var $renew_upgrade_variables;
	var $session_id;
	var $user_credits;
	var $renew_upgrade;
	var $max_ads_reached;
	var $user_currently_subscribed;
	var $invoice_cutoff = 0;

	var $debug_renewals = 0;

//########################################################################

	function Renew_upgrade_sellers_ads($db,$classified_user_id,$language_id,$classified_id=0,$session_id=0,$renew_upgrade=0,$product_configuration=0)
	{
		if ($this->debug_renewals) echo "<br>TOP OF RENEW_UPGRADE_SELLER_ADS<br>\n";
		$this->Site($db,1,$language_id,$classified_user_id,$product_configuration);
		$this->classified_user_id = $classified_user_id;
		$this->classified_id = $classified_id;
		$this->session_id = $session_id;
		$this->renew_upgrade = $renew_upgrade;
		$this->get_user_credits($db);
		if ($this->classified_user_id)
		{
			$this->user_data = $this->get_user_data($db,$this->classified_user_id);
			$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			if ($this->debug_renewals) echo $this->sql_query." is the query <br>\n";
			$price_plan_id_result = $db->Execute($this->sql_query);
			if ($price_plan_id_result->RecordCount() == 1)
			{
				$show_price_plan = $price_plan_id_result->FetchNextObject();
				$this->price_plan_id = $show_price_plan->PRICE_PLAN_ID;
			}
			else
			{
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				if ($this->debug_renewals) echo $this->sql_query." is the query <br>\n";
				$price_plan_id_result = $db->Execute($this->sql_query);
				if ($price_plan_id_result->RecordCount() == 1)
				{
					$show_price_plan = $price_plan_id_result->FetchNextObject();
					$this->price_plan_id = $show_price_plan->PRICE_PLAN_ID;
				}
			}
		}

		if ($this->classified_id)
		{
			//this is a new session...start one
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$this->classified_id;
			if ($this->debug_renewals) echo $this->sql_query." is the query <br>\n";
			$classified_result = $db->Execute($this->sql_query);
			if ($classified_result->RecordCount() == 1)
			{

				$show_category = $classified_result->FetchNextObject();
				$this->category_id = $show_category->CATEGORY;
				$this->classified_data = $show_category;
				if ($this->classified_data->PRICE_PLAN_ID)
				{
					if ($this->debug_renewals) echo "the price plan attached to the classified was chosen<BR>\n";
					//set the current price plan to the price plan connected to the classified ad
					//a price plan attached to the classified ad overrides the default price plan
					//attached to the user.
					//this price plan was chosen at the time the ad was placed.
					$this->price_plan_id = $this->classified_data->PRICE_PLAN_ID;
				}
				else
				{
					if ($this->debug_renewals)
						echo "the price plan attached to the user was chosen<BR>\n";
				}
			}
			$this->remove_renew_upgrade_session($db,$this->session_id);
			//start the new sell session
			$this->sql_query = "insert into ".$this->sell_table."
				(session,time_started,classified_id,terminal_category,renew_upgrade,type)
				values
				(\"".$this->session_id."\",".$this->shifted_time($db).",".$this->classified_id.",".$this->category_id.",".$this->renew_upgrade.",".$show_category->ITEM_TYPE.")";
			$insert_sell_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$insert_sell_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
		}
		else
		{
			//this is an old session...restart it
			$this->sql_query = "select * from ".$this->sell_table." where session = \"".$this->session_id."\" and renew_upgrade = ".$this->renew_upgrade;
			$setup_sell_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$setup_sell_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				$this->setup_error = $this->messages[453];
				return false;
			}
			elseif ($setup_sell_result->RecordCount() == 1)
			{
				$show = $setup_sell_result->FetchNextObject();
				$this->category_id = $show->TERMINAL_CATEGORY;
				$this->classified_id = $show->CLASSIFIED_ID;
				$this->classified_data = $this->get_classified_data($db,$show->CLASSIFIED_ID);
				if ($this->debug_renewals) echo $this->classified_data->PRICE_PLAN_ID." is the price plan id attached to the classified in restart old session<BR>\n";
				if ($this->classified_data->PRICE_PLAN_ID)
				{
					if ($this->debug_renewals)
						echo "the price plan attached to the classified was chosen is restart old session<BR>\n";
					//set the current price plan to the price plan connected to the classified ad
					//a price plan attached to the classified ad overrides the default price plan
					//attached to the user.
					//this price plan was chosen at the time the ad was placed.
					$this->price_plan_id = $this->classified_data->PRICE_PLAN_ID;
				}
				else
				{
					if ($this->debug_renewals)
						echo "the price plan attached to the user was chosen in restart old session<BR>\n";
				}
				$this->renew_upgrade_variables["classified_id"] = $show->CLASSIFIED_ID;
				$this->renew_upgrade_variables["renewal_length"] = $show->CLASSIFIED_LENGTH;
				$this->renew_upgrade_variables["bolding"] = $show->BOLDING;
				$this->renew_upgrade_variables["featured_ad"] = $show->FEATURED_AD;
				$this->renew_upgrade_variables["featured_ad_2"] = $show->FEATURED_AD_2;
				$this->renew_upgrade_variables["featured_ad_3"] = $show->FEATURED_AD_3;
				$this->renew_upgrade_variables["featured_ad_4"] = $show->FEATURED_AD_4;
				$this->renew_upgrade_variables["featured_ad_5"] = $show->FEATURED_AD_5;
				$this->renew_upgrade_variables["better_placement"] = $show->BETTER_PLACEMENT;
				$this->renew_upgrade_variables["attention_getter"] = $show->ATTENTION_GETTER;
				$this->renew_upgrade_variables["attention_getter_choice"] = $show->ATTENTION_GETTER_CHOICE;
				$this->renew_upgrade_variables["bolding_upgrade"] = $show->BOLDING_UPGRADE;
				$this->renew_upgrade_variables["featured_ad_upgrade"] = $show->FEATURED_AD_UPGRADE;
				$this->renew_upgrade_variables["featured_ad_2_upgrade"] = $show->FEATURED_AD_2_UPGRADE;
				$this->renew_upgrade_variables["featured_ad_3_upgrade"] = $show->FEATURED_AD_3_UPGRADE;
				$this->renew_upgrade_variables["featured_ad_4_upgrade"] = $show->FEATURED_AD_4_UPGRADE;
				$this->renew_upgrade_variables["featured_ad_5_upgrade"] = $show->FEATURED_AD_5_UPGRADE;
				$this->renew_upgrade_variables["better_placement_upgrade"] = $show->BETTER_PLACEMENT_UPGRADE;
				$this->renew_upgrade_variables["attention_getter_upgrade"] = $show->ATTENTION_GETTER_UPGRADE;
				$this->renew_upgrade_variables["attention_getter_choice_upgrade"] = $show->ATTENTION_GETTER_CHOICE_UPGRADE;
				$this->renew_upgrade_variables["ad_renewal"] = $show->AD_RENEWAL;
				$this->renew_upgrade_variables["use_credit_for_renewal"] = $show->USE_CREDIT_FOR_RENEWAL;
				$this->renew_upgrade_variables["payment_type"] = $show->PAYMENT_TYPE;
				$this->renew_upgrade_variables["cc_number"] = Site::decrypt($show->CC_NUMBER, $show->DECRYPTION_KEY);
				$this->renew_upgrade_variables["decryption_key"] = 0;
				$this->renew_upgrade_variables["cc_exp_year"] = $show->CC_EXP_YEAR;
				$this->renew_upgrade_variables["cc_exp_month"] = $show->CC_EXP_MONTH;
				$this->renew_upgrade_variables["cvv2_code"] = $show->CVV2_CODE;
				$this->renew_upgrade_variables["item_type"] = $show->TYPE;

				if ($this->debug_renewals)
				{
					echo $this->renew_upgrade_variables["attention_getter"]." is attention_getter<br>\n";
					echo $this->renew_upgrade_variables["attention_getter_upgrade"]." is attention_getter_upgrade<br>\n";
				}
			}
			else
			{
				//incase there are more than one
				//$this->sql_query = "delete from ".$this->sell_table." where session = \"".$this->session_id."\" and renew_upgrade = 1";
				//$setup_sell_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				//if (!$setup_sell_result)
				//{
				//	//$this->body .=$this->sql_query."<bR>\n";
				//	$this->setup_error = $this->messages[453];
				//	return false;
				//}
				//sell session data not there...start over
				//start the new sell session
				$this->sql_query = "insert into ".$this->sell_table."
					(session,time_started,classified_id,terminal_category,renew_upgrade)
					values
					(\"".$this->session_id."\",".$this->shifted_time($db).",".$this->classified_id.",".$this->category_id.",".$this->renew_upgrade.")";
				$insert_sell_result = $db->Execute($this->sql_query);
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				if (!$insert_sell_result)
				{
					if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
					return false;
				}

			}
		}

		$this->get_this_price_plan($db);
		$this->check_maximum_ad_limit($db);
		$this->check_invoice_cutoff($db);
		$this->check_user_subscription($db);

		if ($this->classified_user_id != $this->classified_data->SELLER)
		{
			$this->price_plan = 0;
			$this->price_plan_id = 0;
			$this->classified_data = 0;
			$this->category_id = 0;
			$this->classified_id = 0;
			$this->classified_user_id = 0;
		}
		if ($this->debug_renewals)
		{
			echo $this->renew_upgrade." is renew_upgrade<br>\n";
			echo $this->price_plan." is price_plan<br>\n";
			echo $this->price_plan_id." is price_plan_id<br>\n";
			echo $this->classified_data." is classified_data<br>\n";
			echo $this->category_id." is category_id<br>\n";
			echo $this->classified_id." is classified_id<br>\n";
			echo $this->classified_user_id." is classified_user_id<br>\n";
		}
	} //end of function Renew_upgrade_sellers_ads

//###########################################################

	function get_renew_upgrade_variables($info)
	{
		if($this->debug_renewals)
		{
			echo 'Hello from get_renew_upgrade_variables<Br>';
		}
		if (is_array($info))
		{
			reset ($info);
			foreach ($info as $key => $value)
			{
				$this->renew_upgrade_variables[$key] = $value;
				if($this->debug_renewals)
				{
					echo $key.' is the key<Br>';
					echo $value.' is the value<br>';
				}
			}
		}

	} //end of get_renew_upgrade_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function save_renew_upgrade_variables ($db)
	{
		//move to get_form_variables in site_class.php?
		require_once './classes/site_class.php';
		$unique_key = substr(md5(uniqid(rand(),1)), 0,strlen($this->renew_upgrade_variables["cc_number"]));
		$encrypted_card_num = Site::encrypt($this->renew_upgrade_variables["cc_number"], $unique_key);

		$this->sql_query = "update ".$this->sell_table." set
			classified_length = \"".$this->renew_upgrade_variables["renewal_length"]."\",
			payment_type = \"".$this->renew_upgrade_variables["payment_type"]."\",
			cc_number = \"".$encrypted_card_num."\",
			decryption_key = \"".$unique_key."\",
			cc_exp_year = \"".$this->renew_upgrade_variables["cc_exp_year"]."\",
			cc_exp_month = \"".$this->renew_upgrade_variables["cc_exp_month"]."\",
			cvv2_code = \"".$this->renew_upgrade_variables["cvv2_code"]."\",
			featured_ad = \"".$this->renew_upgrade_variables["featured_ad"]."\",
			featured_ad_2 = \"".$this->renew_upgrade_variables["featured_ad_2"]."\",
			featured_ad_3 = \"".$this->renew_upgrade_variables["featured_ad_3"]."\",
			featured_ad_4 = \"".$this->renew_upgrade_variables["featured_ad_4"]."\",
			featured_ad_5 = \"".$this->renew_upgrade_variables["featured_ad_5"]."\",
			attention_getter = \"".$this->renew_upgrade_variables["attention_getter"]."\",
			attention_getter_choice = \"".$this->renew_upgrade_variables["attention_getter_choice"]."\",
			bolding = \"".$this->renew_upgrade_variables["bolding"]."\",
			better_placement = \"".$this->renew_upgrade_variables["better_placement"]."\",
			featured_ad_upgrade = \"".$this->renew_upgrade_variables["featured_ad_upgrade"]."\",
			featured_ad_2_upgrade = \"".$this->renew_upgrade_variables["featured_ad_2_upgrade"]."\",
			featured_ad_3_upgrade = \"".$this->renew_upgrade_variables["featured_ad_3_upgrade"]."\",
			featured_ad_4_upgrade = \"".$this->renew_upgrade_variables["featured_ad_4_upgrade"]."\",
			featured_ad_5_upgrade = \"".$this->renew_upgrade_variables["featured_ad_5_upgrade"]."\",
			attention_getter_upgrade = \"".$this->renew_upgrade_variables["attention_getter_upgrade"]."\",
			attention_getter_choice_upgrade = \"".$this->renew_upgrade_variables["attention_getter_choice_upgrade"]."\",
			bolding_upgrade = \"".$this->renew_upgrade_variables["bolding_upgrade"]."\",
			better_placement_upgrade = \"".$this->renew_upgrade_variables["better_placement_upgrade"]."\",
			use_credit_for_renewal = \"".$this->renew_upgrade_variables["use_credit_for_renewal"]."\",
			ad_renewal = \"".$this->renew_upgrade_variables["ad_renewal"]."\",
			type = \"".$this->renew_upgrade_variables["item_type"]."\"
			where session = \"".$this->session_id."\"";
		$save_variable_result = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
		if (!$save_variable_result)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
	} //end of function save_renew_upgrade_variables

//####################################################################

	function remove_renew_upgrade_session($db,$sell_session=0)
	{
		if ($sell_session)
		{
			$this->sql_query = "delete from ".$this->sell_table." where session = \"".$sell_session."\" and renew_upgrade = ".$this->renew_upgrade;
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			$delete_sell_result = $db->Execute($this->sql_query);
			if (!$delete_sell_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
		}
	} //end of funciton remove_sell_session

//####################################################################

	function classified_upgrade_form($db)
	{
		$this->page_id = 56;
		$this->get_text($db);
		//echo "hello from almost top of upgrade form<Br>\n";
		//get this users price plan specifics
		if ($this->debug_renewals)
		{
			echo "<BR>TOP OF CLASSIFIED_UPGRADE_FORM<BR>\n";
			echo $this->classified_id." is classified_id in classified_upgrade_form<br>\n";
			echo $this->category_id." is category_id in classified_upgrade_form<br>\n";
			echo $this->price_plan_id." is price_plan_id in classified_upgrade_form<br>\n";
			echo $this->price_plan->TYPE_OF_BILLING." is price_plan type in classified_upgrade_form<br>\n";
			echo $this->classified_user_id." is classified_user_id in classified_upgrade_form<br>\n";
			echo $this->user_data." is user_data in classified_upgrade_form<br>\n";
		}

		if ($this->invoice_cutoff)
		{
			if ($this->debug_renewals) echo "kicking out because of invoice cutoff<BR>\n";
			$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[792])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[793])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=error_message colspan=3>".urldecode($this->messages[3245])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=back_to_my_adds_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=1>".urldecode($this->messages[821])."</a></td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->display_page($db);
			return true;
		}

		if ((!$this->user_currently_subscribed) && ($this->price_plan->TYPE_OF_BILLING == 2))
		{
			if ($this->debug_renewals) echo "not currently subscribed and subscription billing type <BR>\n";
			$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[792])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[793])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=error_message colspan=3>".urldecode($this->messages[1625])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=back_to_my_adds_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=1>".urldecode($this->messages[821])."</a></td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->display_page($db);
			return true;
		}
		elseif ($this->max_ads_reached)
		{
			if ($this->debug_renewals) echo "max ads reached<BR>\n";
			$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
			$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[792])."</td>\n</tr>\n";
			$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[793])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=error_message colspan=3>".urldecode($this->messages[1626])."</td>\n</tr>\n";
			$this->body .="<tr>\n\t<td class=back_to_my_adds_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=1>".urldecode($this->messages[821])."</a></td>\n</tr>\n";
			$this->body .="</table>\n";
			$this->display_page($db);
			return true;
		}
		elseif (($this->classified_id) && ($this->classified_user_id) && ($this->category_id) && ($this->price_plan_id))
		{

			$payment_types_accepted = $this->get_payment_types_accepted($db);
			if (($this->price_plan) && ($this->user_data) && (($payment_types_accepted) || ($this->configuration_data['all_ads_are_free'])))
			{
				$renewable = 0;
				$upgradeable = 0;
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=7&r=".$this->renew_upgrade." method=post>\n";
				$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100%>\n";
				$this->body .="<tr class=page_title>\n\t<td  colspan=3>".urldecode($this->messages[792])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description  colspan=3>\n\t<td colspan=3>".urldecode($this->messages[793])."</td>\n</tr>\n";
				if (strlen($this->error_variables["choose"]) > 0)
					$this->body .="<tr>\n\t<td class=error_message colspan=3>".$this->error_variables["choose"]."</td>\n</tr>\n";
				if ($this->renew_upgrade == 1)
				{
					//SHOW AD RENEWAL COST AND ADDITIONAL FEATURES
					//check to see if in the last few days of the ad to display renewal
					$this->body .="<tr class=renewal_upgrade_section_title>\n\t<td colspan=3>".urldecode($this->messages[1412])."</td>\n</tr>\n";
					$this->body .="<tr>\n\t<td colspan=3>\n\t";
					$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";
					$this->get_cost_of_ad($db);
					if ($this->debug_renewals)
					{
						echo $this->price_plan->TYPE_OF_BILLING." is type of billing<Br>\n";
						echo $this->price_plan->CHARGE_PER_AD_TYPE." is charge per ad type<br>\n";
					}
					if (($this->price_plan->TYPE_OF_BILLING == 1) || ($this->price_plan->TYPE_OF_BILLING == 2))
					{
						$renew_cutoff = ($this->classified_data->ENDS - ($this->configuration_data['days_to_renew'] * 86400));
						$renew_postcutoff = ($this->classified_data->ENDS + ($this->configuration_data['days_to_renew'] * 86400));
						if (($this->configuration_data['days_to_renew']) && ($this->shifted_time($db) > $renew_cutoff) && ($this->shifted_time($db) < $renew_postcutoff))
						{
							$renewable = 1;
							//get price plan specifics
							//fee based billing
							//display the ad cost for renewing the ad
							$this->body .="<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[794])."<Br>";
							//echo $this->price_plan->CHARGE_PER_AD_TYPE." is charge per ad type<br>\n";

							//bolding
						/*$this->body .="<tr>\n\t\t<td  class=cost_field_labels width=50%>".urldecode($this->messages[801])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[802])."</font></td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->BOLDING_PRICE).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[bolding] value=1 ";
						if ($this->renew_upgrade_variables["bolding"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[bolding] value=0 ";
						if (!$this->renew_upgrade_variables["bolding"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";*/

							$this->body .= "<font class=cost_field_descriptions>".urldecode($this->messages[795])."</td>\n\t";
							$this->body .="<td width=20% class=cost_data_values>";
							if ($this->price_plan->CHARGE_PER_AD_TYPE != 2)
							{
								$this->body .= "<font class=cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->cost_of_ad).
								" ".$this->configuration_data['postcurrency']."</font> ";
							}
							elseif ($this->price_plan->CHARGE_PER_AD_TYPE == 2)
							{
								//get the list of costs
							}
							$this->body .= urldecode($this->messages[796])."
								<input type=radio name=c[ad_renewal] value=1 ";
							//if ($this->renew_upgrade_variables["ad_renewal"] == 1)
								$this->body .= "checked";
							$this->body .= ">".urldecode($this->messages[797])."
								<input type=radio name=c[ad_renewal] value=0 ";
							//if ($this->renew_upgrade_variables["ad_renewal"] == 0)
								//$this->body .= "checked";
							$this->body .= ">";
							$this->body .= "</td>";
						//classified duration
							$this->body .="<td class=cost_data_values width=30% align=center>\n\t".urldecode($this->messages[1399])." <select  class=cost_data_values name=c[renewal_length]>\n\t\t";
							//see if there is a
							//$current_category_stuff = $this->get_this_price_plan($db);

							if (($this->price_plan->CHARGE_PER_AD_TYPE == 2) && ($this->price_plan->TYPE_OF_BILLING == 1))
							{
								//pull price plan specific
								if ($this->price_plan->CATEGORY_ID)
									$this->sql_query = "select * from ".$this->price_plan_lengths_table." where
										price_plan_id = ".$this->price_plan_id." and category_id = ".$this->price_plan->CATEGORY_ID." order by length_of_ad asc";
								else
									$this->sql_query = "select * from ".$this->price_plan_lengths_table." where
										price_plan_id = ".$this->price_plan_id." and category_id = 0 order by length_of_ad asc";
								$length_result = $db->Execute($this->sql_query);
								if ($this->debug_renewals)
									echo $this->sql_query."<br>\n";
								if (!$length_result)
								{
									$this->display_basic_duration_dropdown($db);
								}
								elseif ($length_result->RecordCount() > 0)
								{
									while ($show_lengths = $length_result->FetchNextObject())
									{
										$this->body .= "<option value=".$show_lengths->LENGTH_OF_AD;
										if ($this->renew_upgrade_variables["renewal_length"] == $show_lengths->LENGTH_OF_AD)
											   $this->body .= " selected";
										$this->body .= ">".$show_lengths->DISPLAY_LENGTH_OF_AD." ".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_lengths->RENEWAL_CHARGE).
											" ".$this->configuration_data['postcurrency']."</option>";
									}
								}
								else
									$this->display_basic_duration_dropdown($db);
							}
							else
							{
								$this->display_basic_duration_dropdown($db);
							}
							$this->body .= "</select>\n\t</td>\n</tr>\n";
							if (($this->price_plan->ALLOW_CREDITS_FOR_RENEWALS) && ($this->user_credits))
							{
								//echo $this->price_plan->ALLOW_CREDITS_FOR_RENEWALS." is allow credits for renewals<br>\n";
								//echo $this->user_credits." is user_credits<Br>\n";
								$this->body .= "<tr>\n\t\t<td width=50% class=cost_field_labels >".urldecode($this->messages[1615])."</td><td colspan=2>";
								$this->body .= "<table><tr><td class=cost_data_values>
									<input type=radio name=c[use_credit_for_renewal] value=1 ";
								if ($this->renew_upgrade_variables["use_credit_for_renewal"] == 1)
									$this->body .= "checked";
								$this->body .= ">".urldecode($this->messages[796])."<br>
									<input type=radio name=c[use_credit_for_renewal] value=0 ";
								if ($this->renew_upgrade_variables["use_credit_for_renewal"] == 0)
									$this->body .= "checked";
								$this->body .= ">".urldecode($this->messages[797]);
								$this->body .= "</td></tr></table>";
								$this->body .= "</td>\n</tr>\n";
							}
						}
						else
						{
							if ($this->configuration_data['days_to_renew'])
							{
								//cannot be renewed at this time
								$this->body .="<tr>\n\t<td colspan=3>\n\t";
								$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
								$this->body .="<tr>\n\t\t<td class=cost_field_labels >".urldecode($this->messages[799])."</td>\n</tr>\n\t";
								$this->body .= "</table>\n</td>\n</tr>\n";
							}
							else
							{
								$this->body .="<tr>\n\t<td colspan=3>\n\t";
								$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
								$this->body .="<tr>\n\t\t<td  colspan=2 class=cost_field_labels >".urldecode($this->messages[830])."</td>\n\t</tr>\n\t";
								$this->body .= "</table>\n</td>\n</tr>\n";
							}
						}
					}

					if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_bolding_feature']." is USE_BOLDING_FEATURE<br>\n";
						echo $this->price_plan->BOLDING." is BOLDING for this price plan<br>\n";
					}
					if (($this->configuration_data['use_bolding_feature']) && ($this->price_plan->USE_BOLDING))
					{
						//bolding
						$this->body .="<tr>\n\t\t<td  class=cost_field_labels width=50%>".urldecode($this->messages[801])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[802])."</font></td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->BOLDING_PRICE).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[bolding] value=1 ";
						if ($this->renew_upgrade_variables["bolding"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[bolding] value=0 ";
						if (!$this->renew_upgrade_variables["bolding"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";
					}

					if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_better_placement_feature']." is USE_BETTER_PLACEMENT_FEATURE<br>\n";
						echo $this->classified_data->USE_BETTER_PLACEMENT." is USE_BETTER_PLACEMENT for this  price plan<br>\n";
					}
					if (($this->configuration_data['use_better_placement_feature']) && ($this->price_plan->USE_BETTER_PLACEMENT))
					{
						//better placement
						$this->body .="<tr>\n\t\t<td  class=cost_field_labels width=50%>".urldecode($this->messages[803])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[804])."</td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->BETTER_PLACEMENT_CHARGE).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[better_placement] value=1 ";
						if ($this->renew_upgrade_variables["better_placement"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[better_placement] value=0 ";
						if (!$this->renew_upgrade_variables["better_placement"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";
					}

					if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_featured_feature']." is USE_BETTER_PLACEMENT_FEATURE<br>\n";
						echo $this->price_plan->USE_FEATURED_ADS." is USE_BETTER_PLACEMENT for this  price plan<br>\n";
					}
					if (($this->configuration_data['use_featured_feature']) && ($this->price_plan->USE_FEATURED_ADS))
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[805])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[806])."</font></td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[featured_ad] value=1 ";
						if ($this->renew_upgrade_variables["featured_ad"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad] value=0 ";
						if (!$this->renew_upgrade_variables["featured_ad"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";
					}

					if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_featured_feature_2']." is USE_BETTER_PLACEMENT_FEATURE<br>\n";
						echo $this->price_plan->USE_FEATURED_ADS_LEVEL_2." is USE_BETTER_PLACEMENT for this  price plan<br>\n";
					}
					if (($this->configuration_data['use_featured_feature_2'])  && ($this->price_plan->USE_FEATURED_ADS_LEVEL_2))
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2272])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[2273])."</font></td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_2).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[featured_ad_2] value=1 ";
						if ($this->renew_upgrade_variables["featured_ad_2"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_2] value=0 ";
						if (!$this->renew_upgrade_variables["featured_ad_2"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";
					}

					if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_featured_feature_3']." is USE_BETTER_PLACEMENT_FEATURE<br>\n";
						echo $this->price_plan->USE_FEATURED_ADS_LEVEL_3." is USE_BETTER_PLACEMENT for this  price plan<br>\n";
					}
					if (($this->configuration_data['use_featured_feature_3']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_3))
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2274])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[2275])."</font></td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_3).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[featured_ad_3] value=1 ";
						if ($this->renew_upgrade_variables["featured_ad_3"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_3] value=0 ";
						if (!$this->renew_upgrade_variables["featured_ad_3"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";
					}

					if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_featured_feature_4']." is USE_BETTER_PLACEMENT_FEATURE<br>\n";
						echo $this->price_plan->USE_FEATURED_ADS_LEVEL_4." is USE_BETTER_PLACEMENT for this  price plan<br>\n";
					}
					if (($this->configuration_data['use_featured_feature_4']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_4))
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2276])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[2277])."</font></td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_4).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[featured_ad_4] value=1 ";
						if ($this->renew_upgrade_variables["featured_ad_4"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_4] value=0 ";
						if (!$this->renew_upgrade_variables["featured_ad_4"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";
					}

										if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_featured_feature_5']." is USE_BETTER_PLACEMENT_FEATURE<br>\n";
						echo $this->price_plan->USE_FEATURED_ADS_LEVEL_5." is USE_BETTER_PLACEMENT for this  price plan<br>\n";
					}
					if (($this->configuration_data['use_featured_feature_5']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_5))
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2278])."<br>
							<font class=cost_field_descriptions>".urldecode($this->messages[2279])."</font></td>\n\t";
						$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_5).
							" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
							<input type=radio name=c[featured_ad_5] value=1 ";
						if ($this->renew_upgrade_variables["featured_ad_5"]) $this->body .="checked";
						$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_5] value=0 ";
						if (!$this->renew_upgrade_variables["featured_ad_5"]) $this->body .="checked";
						$this->body .="></td>\n\t</tr>\n\t";
					}

										if ($this->debug_renewals)
					{
						echo $this->configuration_data['use_attention_getters']." is USE_ATTENTION_GETTERS<br>\n";
						echo $this->price_plan->USE_ATTENTION_GETTERS." is USE_ATTENTION_GETTERS for this  price plan<br>\n";
					}
					if (($this->configuration_data['use_attention_getters']) && ($this->price_plan->USE_ATTENTION_GETTERS))
					{
						$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 10";
						//echo $this->sql_query."<br>\n";
						$attention_getters_result = $db->Execute($this->sql_query);
						if (!$attention_getters_result)
						{
							//echo $this->sql_query."<br>\n";
							$this->setup_error = $this->messages[453];
							return false;
						}
						elseif ($attention_getters_result->RecordCount() > 0)
						{
							//attention getters
							$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[807])."<br>
								<font class=cost_field_descriptions>".urldecode($this->messages[808])."</font>";
							if (strlen($this->error_variables["attention_getter"]) > 0)
								$this->body .= "<br><font class=error_message>".$this->error_variables["attention_getter"]."</font>";
							$this->body .= "</td>\n\t";
							$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->ATTENTION_GETTER_PRICE).
								" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
								<input type=radio name=c[attention_getter] value=1 ";
							if ($this->renew_upgrade_variables["attention_getter"]) $this->body .="checked";
							$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[attention_getter] value=0 ";
							if (!$this->renew_upgrade_variables["attention_getter"]) $this->body .="checked";
							$this->body .="><br><table cellpadding=1 cellspacing=1 border=0 align=center>";
							while ($show_attention_getter = $attention_getters_result->FetchNextObject())
							{
								$this->body .= "<tr>\n\t<td valign=middle class=cost_data_values ><img src=\"".$show_attention_getter->VALUE."\" border=0 hspace=2>
									<input type=radio name=c[attention_getter_choice] value=".$show_attention_getter->CHOICE_ID;
								if (($this->renew_upgrade_variables["attention_getter_choice"] == $show_attention_getter->CHOICE_ID) ||
									($this->renew_upgrade_variables["attention_getter_choice_upgrade"] == $show_attention_getter->CHOICE_ID))
									$this->body .= " checked";
								$this->body .= "></td>\n\t</tr>\n";
							}
							$this->body .= "</table></td>\n\t</tr>\n\t";
						}
					}
					$this->body .="</table>\n\t</td>\n</tr>\n";
				}
				elseif ($this->renew_upgrade == 2)
				{
					//check to see if the early days of the ad so can upgrade
					$this->body .="<tr class=renewal_upgrade_section_title>\n\t<td colspan=3>".urldecode($this->messages[1413])."</td>\n</tr>\n";
					$this->body .="<tr>\n\t<td colspan=3>\n\t";
					$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";

					$upgrade_cutoff = ($this->classified_data->DATE + ($this->configuration_data['days_can_upgrade'] * 86400));
					if (($this->configuration_data['days_can_upgrade']) && ($this->shifted_time($db) < $upgrade_cutoff))
					{
						if ((($this->configuration_data['use_bolding_feature']) && ($this->classified_data->BOLDING == 0)) ||
							(($this->configuration_data['use_better_placement_feature']) && ($this->classified_data->BETTER_PLACEMENT == 0)) ||
							(($this->configuration_data['use_featured_feature']) && ($this->classified_data->FEATURED_AD == 0)) ||
							(($this->configuration_data['use_featured_feature_2']) && ($this->classified_data->FEATURED_AD_2 == 0)) ||
							(($this->configuration_data['use_featured_feature_3']) && ($this->classified_data->FEATURED_AD_3 == 0)) ||
							(($this->configuration_data['use_featured_feature_4']) && ($this->classified_data->FEATURED_AD_4 == 0)) ||
							(($this->configuration_data['use_featured_feature_5']) && ($this->classified_data->FEATURED_AD_5 == 0)) ||
							(($this->configuration_data['use_attention_getters']) && ($this->classified_data->ATTENTION_GETTER == 0)))
						{
							$upgradeable = 1;
							$this->body .="<tr class=page_description >\n\t<td colspan=3>".urldecode($this->messages[800])."</td>\n</tr>\n";
							if (($this->configuration_data['use_bolding_feature']) && ($this->price_plan->USE_BOLDING) && ($this->classified_data->BOLDING == 0))
							{
								//bolding
								$this->body .="<tr>\n\t\t<td  valign=top width=50%><font class=cost_field_labels >".urldecode($this->messages[801])."</font><br>
									<font class=cost_field_descriptions>".urldecode($this->messages[802])."</font></td>\n\t";
								$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->BOLDING_PRICE).
									" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
									<input type=radio name=c[bolding_upgrade] value=1 ";
								if ($this->renew_upgrade_variables["bolding_upgrade"]) $this->body .="checked";
								$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[bolding_upgrade] value=0 ";
								if (!$this->renew_upgrade_variables["bolding_upgrade"]) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}

							if (($this->configuration_data['use_better_placement_feature']) && ($this->price_plan->USE_BETTER_PLACEMENT) && ($this->classified_data->BETTER_PLACEMENT == 0))
							{
								//better placement
								$this->body .="<tr>\n\t\t<td  valign=top width=50%><font class=cost_field_labels >".urldecode($this->messages[803])."</font><br>
									<font class=cost_field_descriptions>".urldecode($this->messages[804])."</td>\n\t";
								$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->BETTER_PLACEMENT_CHARGE).
									" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
									<input type=radio name=c[better_placement_upgrade] value=1 ";
								if ($this->renew_upgrade_variables["better_placement_upgrade"]) $this->body .="checked";
								$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[better_placement_upgrade] value=0 ";
								if (!$this->renew_upgrade_variables["better_placement_upgrade"]) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}

							if (($this->configuration_data['use_featured_feature']) && ($this->price_plan->USE_FEATURED_ADS) && ($this->classified_data->FEATURED_AD == 0))
							{
								//featured ad
								$this->body .="<tr>\n\t\t<td   valign=top width=50%><font class=cost_field_labels >".urldecode($this->messages[805])."</font><br>
									<font class=cost_field_descriptions>".urldecode($this->messages[806])."</font></td>\n\t";
								$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE).
									" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
									<input type=radio name=c[featured_ad_upgrade] value=1 ";
								if ($this->renew_upgrade_variables["featured_ad_upgrade"]) $this->body .="checked";
								$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_upgrade] value=0 ";
								if (!$this->renew_upgrade_variables["featured_ad_upgrade"]) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}

							if (($this->configuration_data['use_featured_feature_2']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_2) && ($this->classified_data->FEATURED_AD_2 == 0))
							{
								//featured ad
								$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2272])."<br>
									<font class=cost_field_descriptions>".urldecode($this->messages[2273])."</font></td>\n\t";
								$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_2).
									" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
									<input type=radio name=c[featured_ad_2_upgrade] value=1 ";
								if ($this->renew_upgrade_variables["featured_ad_2_upgrade"]) $this->body .="checked";
								$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_2_upgrade] value=0 ";
								if (!$this->renew_upgrade_variables["featured_ad_2_upgrade"]) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}

							if (($this->configuration_data['use_featured_feature_3']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_3) && ($this->classified_data->FEATURED_AD_3 == 0))
							{
								//featured ad
								$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2274])."<br>
									<font class=cost_field_descriptions>".urldecode($this->messages[2275])."</font></td>\n\t";
								$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_3).
									" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
									<input type=radio name=c[featured_ad_3_upgrade] value=1 ";
								if ($this->renew_upgrade_variables["featured_ad_3_upgrade"]) $this->body .="checked";
								$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_3_upgrade] value=0 ";
								if (!$this->renew_upgrade_variables["featured_ad_3_upgrade"]) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}

							if (($this->configuration_data['use_featured_feature_4']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_4) && ($this->classified_data->FEATURED_AD_4 == 0))
							{
								//featured ad
								$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2276])."<br>
									<font class=cost_field_descriptions>".urldecode($this->messages[2277])."</font></td>\n\t";
								$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_4).
									" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
									<input type=radio name=c[featured_ad_4_upgrade] value=1 ";
								if ($this->renew_upgrade_variables["featured_ad_4_upgrade"]) $this->body .="checked";
								$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_4_upgrade] value=0 ";
								if (!$this->renew_upgrade_variables["featured_ad_4_upgrade"]) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}

							if (($this->configuration_data['use_featured_feature_5']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_5) && ($this->classified_data->FEATURED_AD_5 == 0))
							{
								//featured ad
								$this->body .="<tr>\n\t\t<td class=cost_field_labels width=50%>".urldecode($this->messages[2278])."<br>
									<font class=cost_field_descriptions>".urldecode($this->messages[2279])."</font></td>\n\t";
								$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->FEATURED_AD_PRICE_5).
									" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
									<input type=radio name=c[featured_ad_5_upgrade] value=1 ";
								if ($this->renew_upgrade_variables["featured_ad_5_upgrade"]) $this->body .="checked";
								$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[featured_ad_5_upgrade] value=0 ";
								if (!$this->renew_upgrade_variables["featured_ad_5_upgrade"]) $this->body .="checked";
								$this->body .="></td>\n\t</tr>\n\t";
							}


							if (($this->configuration_data['use_attention_getters']) && ($this->price_plan->USE_ATTENTION_GETTERS) && ($this->classified_data->ATTENTION_GETTER == 0))
							{
								$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 10";
								//echo $this->sql_query."<br>\n";
								$attention_getters_result = $db->Execute($this->sql_query);
								if (!$attention_getters_result)
								{
									//$this->body .=$this->sql_query."<br>\n";
									$this->setup_error = $this->messages[453];
									return false;
								}
								elseif ($attention_getters_result->RecordCount() > 0)
								{
									//attention getters
									$this->body .="<tr>\n\t\t<td  valign=top width=50%><font class=cost_field_labels >".urldecode($this->messages[807])."</font><br>
										<font class=cost_field_descriptions>".urldecode($this->messages[808])."</font>";
									if (strlen($this->error_variables["attention_getter_upgrade"]) > 0)
										$this->body .= "<br><font class=error_message>".$this->error_variables["attention_getter_upgrade"]."</font>";
									$this->body .= "</td>\n\t";
									$this->body .="<td class=cost_data_values width=50% colspan=2>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->price_plan->ATTENTION_GETTER_PRICE).
										" ".$this->configuration_data['postcurrency']." ".urldecode($this->messages[796])."
										<input type=radio name=c[attention_getter_upgrade] value=1 ";
									if ($this->renew_upgrade_variables["attention_getter_upgrade"]) $this->body .="checked";
									$this->body .="> ".urldecode($this->messages[797])."<input type=radio name=c[attention_getter_upgrade] value=0 ";
									if (!$this->renew_upgrade_variables["attention_getter_upgrade"]) $this->body .="checked";
									$this->body .="><br><table cellpadding=1 cellspacing=1 border=0 align=center>";
									while ($show_attention_getter = $attention_getters_result->FetchNextObject())
									{
										$this->body .= "<tr>\n\t<td valign=middle class=cost_data_values ><img src=\"".$show_attention_getter->VALUE."\" border=0 hspace=2>
											<input type=radio name=c[attention_getter_choice_upgrade] value=".$show_attention_getter->CHOICE_ID;
										if ($this->renew_upgrade_variables["attention_getter_choice_upgrade"] == $show_attention_getter->CHOICE_ID)
											$this->body .= " checked";
										$this->body .= "></td>\n\t</tr>\n";
									}
									$this->body .= "</table></td>\n\t</tr>\n\t";
								}
							}
						}
						else
						{
							$this->body .="<tr>\n\t\t<td colspan=3 class=cost_field_labels >".urldecode($this->messages[832])."</td>\n\t</tr>\n\t";
						}
					}
					else
					{
						//cannot upgrade
						if ($this->configuration_data['days_can_upgrade'])
						{
							$this->body .="<tr>\n\t\t<td colspan=3 class=cost_field_labels >".urldecode($this->messages[832])."</td>\n\t</tr>\n\t";
						}
						else
						{
							$this->body .="<tr>\n\t\t<td colspan=3 class=cost_field_labels >".urldecode($this->messages[831])."</td>\n\t</tr>\n\t";
						}
					}
					$this->body .="</table>\n\t</td>\n</tr>\n";
				}

				if ((($upgradeable) || ($renewable)) && (!$this->configuration_data['all_ads_are_free']))
				{
					//PAY CHOICE(S)
					$this->body .="<tr class=renewal_upgrade_section_title>\n\t<td colspan=3>".urldecode($this->messages[1414])."</td>\n</tr>\n";
					$this->body .= "<tr><td colspan=3>\n\t";
					$this->body .="<table cellpadding=3 cellspacing=0 border=1 align=center width=100%>\n\t";
					$this->body .="<tr class=page_description >\n\t<td colspan=3>".urldecode($this->messages[809]);
					if ($this->error_variables["payment_type"])
						$this->body .="<Br><br><font class=error_message>".urldecode($this->messages[810])."</font><br><br>";
					if ($this->error_variables["account_balance"])
						$this->body .="<Br><br><font class=error_message>".urldecode($this->error_variables["account_balance"])."</font><br><br>";

					$this->body .="</td>\n</tr>\n";
					while ($show_payment = $payment_types_accepted->FetchNextObject())
					{
						switch ($show_payment->TYPE)
						{
							case 1:
							{
								if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								{
									//cash
									$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[1599])."</font><br>
									<font class=payment_choices_descriptions>".urldecode($this->messages[1600])."</td>\n\t";
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] checked value=1>&nbsp;</td>\n\t</tr>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=1 ";
										if ($this->renew_upgrade_variables["payment_type"] == 1) $this->body .="checked";
										$this->body .="></td>\n\t</tr>\n\t";
									}
								}
							}
							break;
							case 2:
							{
								if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								{
									//credit card
									$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";
									if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
									$cc_result = $db->Execute($this->sql_query);
									if (!$cc_result)
									{
										if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
										$this->setup_error = $this->messages[453];
										return false;
									}
									elseif ($cc_result->RecordCount() == 1)
									{
										$show_cc_choice = $cc_result->FetchNextObject();

									}
									$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[812])."</font><br>
									<font class=payment_choices_descriptions>".urldecode($this->messages[813])."</td>\n\t";
									if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
									{
										if ($payment_types_accepted->RecordCount() == 1)
										{
											//this is the only choice so has hidden variable saying this is the type requested
											$this->body .="<td width=10% ><input type=radio name=c[payment_type] checked value=2>&nbsp;</td>\n\t<td width=40% align=center>\n\t";
										}
										else
										{
											//this is a choice among many
											$this->body .="<td width=10% ><input type=radio name=c[payment_type] value=2 ";
											if ($this->renew_upgrade_variables["payment_type"] == 2) $this->body .="checked";
											$this->body .="></td>\n\t<td width=40% align=center class=payment_choices_cc_number_values>\n\t";
										}
										$this->body .="
											<table cellpadding=3 cellspacing=1 border=0 align=center width=100% class=payment_choices_cc_number_values>
												<tr>
													<td width=50% align=right>";
										if ($this->error_variables["cc_number"])
											$this->body .="
														<font class=error_message>".$this->error_variables["cc_number"]."</font>";
										if ($this->error_variables["cc_expiration"])
											$this->body .="
														<font class=error_message>".$this->error_variables["cc_expiration"]."</font>";
										$this->body .= urldecode($this->messages[814]);
										$this->body .="
													</td>
													<td width=50% align=left>
														<input type=text name=c[cc_number] size=20><br>
													</td>
												</tr>";
										if ($show_cc_choice->CC_ID==7  || $show_cc_choice->CC_ID==9)
										{
											$this->body .="
												<tr>
													<td width=50% align=right>";
											$this->body .= "
														<a href=\"javascript:winimage('./images/cvv2_code.gif',500,200)\">".
														urldecode($this->messages[200108])."</a>";
											$this->body .= "
													</td>
													<td width=50% align=left>
														<input type=text name=c[cvv2_code] size=4>
													</td>
												</tr>";
										}
										$this->body .="
												<tr>
													<td width=50% align=right>".urldecode($this->messages[815])."</td>
													<td width=50% align=left>";
										$this->body .= "<select name=c[cc_exp_month]>";
										for ($i=1;$i<13;$i++)
										{
											$this->body .="
															<option>".sprintf("%02d",$i)."</option>";
										}
										$this->body .= "</select>";
										$this->display_year_dropdown("c[cc_exp_year]");
										$this->body .= "
													</td>
												</tr>
											</table>";
									}
									elseif (($show_cc_choice->CC_ID == 2) ||  ($show_cc_choice->CC_ID == 5))
									{
										if ($payment_types_accepted->RecordCount() == 1)
										{
											//this is the only choice so has hidden variable saying this is the type requested
											$this->body .= "<td colspan=2 width=50% valign=top><input type=radio name=c[payment_type] checked value=2>&nbsp;";
										}
										else
										{
											//this is a choice among many
											$this->body .= "<td colspan=2 width=50% valign=top><input type=radio name=c[payment_type] value=2 ";
											if ($this->renew_upgrade_variables["payment_type"] == 2) $this->body .= "checked";
											$this->body .= ">";
										}
									}
									$this->body .="</td>\n\t</tr>\n\t";
								}
							}
							break;
							case 3:
							{
								if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								{
									//paypal
									$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[816])."</font><br>
									<font class=payment_choices_descriptions>".urldecode($this->messages[817])."</td>\n\t";
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td width=50% colspan=2 ><input type=radio checked name=c[payment_type] value=3>&nbsp;</td>\n\t</tr>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td width=50% colspan=2 ><input type=radio name=c[payment_type] value=3 ";
										if ($this->renew_upgrade_variables["payment_type"] == 3) $this->body .="checked";
										$this->body .="></td>\n\t</tr>\n\t";
									}
								}
							}
							break;
							case 4:
							{
								{
									//money order
									$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[1601])."</font><br>
									<font class=payment_choices_descriptions>".urldecode($this->messages[1602])."</td>\n\t";
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=4>&nbsp;</td>\n\t</tr>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=4 ";
										if ($this->renew_upgrade_variables["payment_type"] == 4) $this->body .="checked";
										$this->body .="></td>\n\t</tr>\n\t";
									}
								}
							}
							break;
							case 5:
							{
								if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								{
									//check
									$this->body .="<tr>\n\t\t<td width=50%><font class=payment_choices_labels>".urldecode($this->messages[1603])."</font><br>
									<font class=payment_choices_descriptions>".urldecode($this->messages[1604])."</td>\n\t";
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td  width=50% colspan=2 valign=top><input type=radio checked name=c[payment_type] value=5>&nbsp;</td>\n\t</tr>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td  width=50% colspan=2 valign=top><input type=radio name=c[payment_type] value=5 ";
										if ($this->renew_upgrade_variables["payment_type"] == 5) $this->body .="checked";
										$this->body .="></td>\n\t</tr>\n\t";
									}
								}
							}
							break;
							case 6:
							{
								if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								{
									//worldpay
									$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[818])."</font><br>
									<font class=payment_choices_descriptions>".urldecode($this->messages[819])."</td>\n\t";
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td width=50% colspan=2 ><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td width=50% colspan=2 ><input type=radio name=c[payment_type] value=6 ";
										if ($this->renew_upgrade_variables["payment_type"] == 6) $this->body .="checked";
										$this->body .="></td>\n\t</tr>\n\t";
									}
								}
							}
							break;

							case 7: //site balance
							{
								if ($this->debug_renewals)
								{
									echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE<br>\n";
									echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<br>\n";
									echo $this->user_data->ACCOUNT_BALANCE." is the ACCOUNT_BALANCE<br>\n";
								}
								if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) && ($this->user_data->ACCOUNT_BALANCE > 0))
									|| (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								{
									if ($this->debug_renewals) echo "displaying account balance option<br>\n";
									//will check to see if balance is enough to pay for within transaction detail check

									if (!$this->configuration_data['positive_balances_only'])
									{
										$this->body .="<tr>\n\t\t<td class=payment_choices_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3256])."</font><br>
											<font class=payment_choices_descriptions>";
										$this->body .= urldecode($this->messages[3257])."<br>".urldecode($this->messages[3258]);

										//get current accumulated but unbilled charges
										$this->sql_query = "select * from ".$this->balance_transactions." where (auction_id != 0 or ad_id != 0 or subscription_renewal != 0) and cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$this->classified_user_id;
										if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
										$invoice_total_result = $db->Execute($this->sql_query);
										if (!$invoice_total_result)
										{
											if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
											return false;
										}
										elseif ($invoice_total_result->RecordCount() > 0)
										{
											$to_be_invoiced = 0;
											while ($show_invoices = $invoice_total_result->FetchNextObject())
											{
												$to_be_invoiced = $to_be_invoiced + $show_invoices->AMOUNT;
											}
											$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$to_be_invoiced)." ".$this->configuration_data['postcurrency'];
										}
										else
										{
											$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%0.02f",0)." ".$this->configuration_data['postcurrency'];
										}
										$this->body .= "</td>\n\t";
									}
									else
									{
										$this->body .="<tr>\n\t\t<td class=payment_choices_labels width=50%><font class=payment_choices_field_labels>".urldecode($this->messages[3186])."</font><br>";
										if (strlen(trim($this->error_message["account_balance"])) > 0)
											$this->body .= "<font class=error_message>".urldecode($this->messages[3259])."</font><br>";
										$this->body .= "<font class=payment_choices_descriptions><br>".
											urldecode($this->messages[3187])."<br><br>".urldecode($this->messages[3260]).$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->user_data->ACCOUNT_BALANCE)." ".$this->configuration_data['postcurrency']."\n\t";
										$this->body .= "</td>\n\t";
									}

									$this->body .="<td width=50% colspan=2 valign=top><input type=radio name=c[payment_type] ";
									if ($this->renew_upgrade_variables["payment_type"] == 7) $this->body .="checked";
									elseif ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
										($this->configuration_data['site_balance_override'] == 0)) ||
										(!$this->configuration_data['use_account_balance']) ||
										(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
										$this->body .= "checked";
									$this->body .= " value=7>&nbsp;</td>\n\t</tr>\n\t";
								}
							}
							break;
							case 8:
							{
								//NOCHEX
								/*if ((($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']) &&
									($this->configuration_data['site_balance_override'] == 0)) ||
									(!$this->configuration_data['use_account_balance']) ||
									(($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only'])))
								{
									//worldpay
									$this->body .="<tr>\n\t\t<td  width=50%><font class=payment_choices_labels>".urldecode($this->messages[])."</font><br>
									<font class=payment_choices_descriptions>".urldecode($this->messages[])."</td>\n\t";
									if ($payment_types_accepted->RecordCount() == 1)
									{
										//this is the only choice so has hidden variable saying this is the type requested
										$this->body .="<td width=50% colspan=2 ><input type=radio checked name=c[payment_type] value=6>&nbsp;</td>\n\t</tr>\n\t";
									}
									else
									{
										//this is a choice among many
										$this->body .="<td width=50% colspan=2 ><input type=radio name=c[payment_type] value=6 ";
										if ($this->renew_upgrade_variables["payment_type"] == 6) $this->body .="checked";
										$this->body .="></td>\n\t</tr>\n\t";
									}
								}*/
							}
							break;
						}
					}
					$this->body .="</table>\n\t</td>\n</tr>\n";
				}

				$this->body .="<tr>\n\t<td align=center colspan=2 class=>\n\t<br><input type=submit name=z class=submit_button value=\"".urldecode($this->messages[820])."\">\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td class=back_to_my_adds_link><a href=".$this->configuration_data['classifieds_url']."?a=4&b=1>".urldecode($this->messages[821])."</a></td>\n</tr>\n";
				$this->body .="</table>\n";
				$this->body .="</form>\n";

				$this->display_page($db);
				return true;
			}
			else
			{
				if ($this->debug_renewals) echo "payment types not returning correctly<BR>\n";
				//echo "something wrong 2<br>\n";
				return false;
			}
		}
		else
		{
			return false;
		}

	} //end of function classified_upgrade_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_transaction_variables($db)
	{
		$this->page_id = 56;
		$this->get_text($db);
		$this->error = 0;
		unset($this->error_variables);
		$this->error_variables = array();
		//echo $this->renew_upgrade." is renew_upgrade<bR>\n";
		if ($this->renew_upgrade == 1)
		{
			$renew_cutoff = ($this->classified_data->ENDS - ($this->configuration_data['days_to_renew'] * 86400));
			$renew_postcutoff = ($this->classified_data->ENDS + ($this->configuration_data['days_to_renew'] * 86400));
			if (($this->configuration_data['days_to_renew']) && ($this->shifted_time($db) > $renew_cutoff) && ($this->shifted_time($db) < $renew_postcutoff))
			{
				//check to see if chosen and cost of renewal
				if ($this->renew_upgrade_variables["ad_renewal"])
				{
					$renewing = 1;
					if (!$this->renew_upgrade_variables["renewal_length"])
					{
						if ($this->debug_renewals)
						{
							echo "error because no renewal_length<br>\n";
						}
						$this->error++;
						$this->error_variables["choose"] = urldecode($this->messages[836]);
					}
				}
				else
				{
					$renewing = 0;
					if ((($this->configuration_data['use_bolding_feature']) && ($this->renew_upgrade_variables["bolding"]))
						|| (($this->configuration_data['use_better_placement_feature']) && ($this->renew_upgrade_variables["better_placement"]))
						|| (($this->configuration_data['use_featured_feature']) && ($this->renew_upgrade_variables["featured_ad"]))
						|| (($this->configuration_data['use_featured_feature_2']) && ($this->renew_upgrade_variables["featured_ad_2"]))
						|| (($this->configuration_data['use_featured_feature_3']) && ($this->renew_upgrade_variables["featured_ad_3"]))
						|| (($this->configuration_data['use_featured_feature_4']) && ($this->renew_upgrade_variables["featured_ad_4"]))
						|| (($this->configuration_data['use_featured_feature_5']) && ($this->renew_upgrade_variables["featured_ad_5"]))
						|| (($this->configuration_data['use_attention_getters']) && ($this->renew_upgrade_variables["attention_getter"])))
					{
						$upgrading = 1;
					}
					else
					{
						if ($this->debug_renewals)
						{
							echo "error because feature choice issue<br>\n";
						}
						$this->error++;
						$this->error_variables["choose"] = urldecode($this->messages[836]);
					}
				}
			}
		}

		if ($this->renew_upgrade == 2)
		{
			//check to see if the early days of the ad so can upgrade
			$upgrade_cutoff = ($this->classified_data->DATE + ($this->configuration_data['days_can_upgrade'] * 86400));
			if (($this->configuration_data['days_can_upgrade']) && ($this->shifted_time($db) < $upgrade_cutoff))
			{
				//check to see if chosen and cost of upgrades
				if ((($this->configuration_data['use_bolding_feature']) && ($this->renew_upgrade_variables["bolding_upgrade"]))
					|| (($this->configuration_data['use_better_placement_feature']) && ($this->renew_upgrade_variables["better_placement_upgrade"]))
					|| (($this->configuration_data['use_featured_feature']) && ($this->renew_upgrade_variables["featured_ad_upgrade"]))
					|| (($this->configuration_data['use_featured_feature_2']) && ($this->renew_upgrade_variables["featured_ad_2_upgrade"]))
					|| (($this->configuration_data['use_featured_feature_3']) && ($this->renew_upgrade_variables["featured_ad_3_upgrade"]))
					|| (($this->configuration_data['use_featured_feature_4']) && ($this->renew_upgrade_variables["featured_ad_4_upgrade"]))
					|| (($this->configuration_data['use_featured_feature_5']) && ($this->renew_upgrade_variables["featured_ad_5_upgrade"]))
					|| (($this->configuration_data['use_attention_getters']) && ($this->renew_upgrade_variables["attention_getter_upgrade"])))
				{
					$upgrading = 1;
				}
				else
				{
					$upgrading = 0;
				}
			}
		}

		if (($upgrading) || ($renewing))
		{
			$this->get_cost_of_ad($db);
			//echo $this->cost_of_ad." is the total in check transaction<br>\n";
			//echo $this->subtotal." is the subtotal<br>\n";
			if ($this->subtotal != 0)
			{
				if (!$this->renew_upgrade_variables["payment_type"])
				{
					//error in classified_title was not entered
					$this->error++;
					$this->error_variables["payment_type"] = urldecode($this->messages[840]);
					$this->body .="bad transaction type<br>\n";
				}
			}

			if (($this->renew_upgrade_variables["payment_type"] == 2) && ($this->subtotal != 0))
			{
				//cc_number
				//put verification script in
				$this->sql_query = "select cc_id from ".$this->cc_choices." where chosen_cc = 1";
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				$cc_choice_result = $db->Execute($this->sql_query);
				if (!$cc_choice_result)
				{
					if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
					return false;
				}
				elseif ($cc_choice_result->RecordCount() == 1)
				{
					$show_cc_choice = $cc_choice_result->FetchNextObject();
					if (($show_cc_choice->CC_ID == 1) || ($show_cc_choice->CC_ID == 3) || ($show_cc_choice->CC_ID == 4) || ($show_cc_choice->CC_ID >= 6))
					{
						if (strlen(trim($this->renew_upgrade_variables["cc_number"])) == 0)
						{
							$this->error++;
							$this->error_variables["cc_number"] = urldecode($this->messages[838]);
						}

						//check date of expiration
						$current_year = date("y");
						if (($this->renew_upgrade_variables["cc_exp_year"] < $current_year) || (($this->renew_upgrade_variables["cc_exp_year"] == $current_year)
							&& ($this->renew_upgrade_variables["cc_exp_month"] < date("m"))))
						{
							$this->error++;
							$this->error_variables["cc_expiration"] = urldecode($this->messages[839]);
						}
					}
				}
				else
					return false;
			}

			if ($this->renew_upgrade_variables["payment_type"] == 7)
			{
				$user_data = $this->get_user_data($db,$this->classified_user_id);
				if ($this->debug_renewals)
				{
					echo "checking account type payment choice<bR>\n";
					echo $this->subtotal." is the subtotal<bR>\n";
					echo $user_data->ACCOUNT_BALANCE." is the account_balance<bR>\n";
				}
				//account balance payment type

				if ($this->subtotal > $user_data->ACCOUNT_BALANCE)
				{
					$this->error++;
					$this->error_variables["account_balance"] = urldecode($this->messages[3253]);
				}
			}

			if ($renewing)
			{
				if ($this->renew_upgrade_variables["attention_getter"])
				{
					//check attention getter choice
					if ($this->renew_upgrade_variables["attention_getter_choice"] == 0)
					{
						$this->error++;
						$this->error_variables["attention_getter"] = urldecode($this->messages[837]);
					}
				}
			}

			if ($upgrading)
			{
				if ($this->renew_upgrade_variables["attention_getter_upgrade"])
				{
					//check attention getter choice
					if ($this->renew_upgrade_variables["attention_getter_choice_upgrade"] == 0)
					{
						$this->error++;
						$this->error_variables["attention_getter_upgrade"] = urldecode($this->messages[837]);
					}
				}
			}
		}
		else
		{
			//some action must be taken
			$this->error++;
			$this->error_variables["choose"] = urldecode($this->messages[836]);
		}

		if ($this->debug_renewals)
		{
			echo $this->error." is the error count<br>\n";
			reset($this->error_variables);
			foreach ($this->error_variables as $key => $value)
				echo $key." is the key to ".$value."<br>\n";
		}

		if ($this->error == 0)
			return true;
		else
			return false;
	} //end of function check_transaction_variables

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function final_approval_form($db)
	{
		$this->page_id = 57;
		$this->get_text($db);

		//save transaction data
		//save the transaction data overwriting
		//update transaction data into the classifieds table


		//get totals and taxes if any
		if (($this->classified_id) && ($this->classified_user_id) && ($this->category_id) && ($this->price_plan_id))
		{
			//$this->body .=$this->cost_of_ad." is subtotal cost of ad in final_approval_form<br>\n";
			//$this->body .=$this->subtotal." is subtotal returned in final_approval_form<br>\n";
			$user_data = $this->get_user_data($db);
			$this->tax = $this->get_tax($db,$user_data);
			$this->total = $this->subtotal + $this->tax;
			$renewable = 0;
			$upgradeable = 0;

			//check to see if in the last few days of the ad to display renewal
			$renew_cutoff = ($this->classified_data->ENDS - ($this->configuration_data['days_to_renew'] * 86400));
			$renew_postcutoff = ($this->classified_data->ENDS + ($this->configuration_data['days_to_renew'] * 86400));
			if (($this->configuration_data['days_to_renew']) && ($this->shifted_time($db) > $renew_cutoff) && ($this->shifted_time($db) < $renew_postcutoff))
			{
				$renewable = 1;
				//check to see if chosen and cost of renewal
			}

			//check to see if the early days of the ad so can upgrade
			$upgrade_cutoff = ($this->classified_data->DATE + ($this->configuration_data['days_can_upgrade'] * 86400));
			if (($this->configuration_data['days_can_upgrade']) && ($this->shifted_time($db) < $upgrade_cutoff))
			{
				$upgradeable = 1;
				//check to see if chosen and cost of upgrades
			}

			if (($upgradeable) || ($renewable))
			{
				$this->body .= "<form onSubmit=\"this.z.disabled=true\" action=".$this->configuration_data['classifieds_file_name']."?a=7&d=final_accepted&r=".$this->renew_upgrade." method=post>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
				$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[822])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[823])."</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t<td>";
				if ($this->total != 0)
				{
					$user_data = $this->user_data;
					//check pay choice information
					switch ($this->renew_upgrade_variables["payment_type"])
					{
						case 1: //cash
							$this->body .= urldecode($this->messages[1605]);
						break;
						case 2: //credit card
							$this->body .= urldecode($this->messages[827]);
						break;
						case 3: //paypal
							$this->body .= urldecode($this->messages[828]);
						break;
						case 4: //money order
							$this->body .= urldecode($this->messages[1606]);
						break;
						case 5: //check
							$this->body .= urldecode($this->messages[1607]);
						break;
						case 6: //worldpay
							$this->body .= urldecode($this->messages[829]);
						break;
						case 7: //site balance/invoice
							$this->body .= urldecode($this->messages[3188]);
						break;
						case 8: //NOCHEX
							//$this->body .= urldecode($this->messages[]);
						break;
						default:
							return false;
					} //end of switch ($this->renew_upgrade_variables["payment_type"])

				}
				$this->body .= $payment_type_message."</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td align=center>\n\t<table cellpadding=3 cellspacing=1>\n\t";
				$this->body .="<tr class=transaction_totals_header>\n\t<td colspan=2>".urldecode($this->messages[824])."</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td  class=features_renewal_column_header>".urldecode($this->messages[825])."</td>\n\t";
				$this->body .="<td class=cost_column_header>".urldecode($this->messages[826])."</td>\n</tr>\n";
				//show transaction totals
				//cost of ad

				if (($renewable) && ($this->renew_upgrade == 1))
				{
					$this->body .="<tr>\n\t<td  width=50% class=ad_cost_field_labels>";
					$this->body .=urldecode($this->messages[862]);
					if ($this->total == 0)
					{
						$this->body .="<input type=hidden name=c[ad_renewal] value=1>";
						$this->body .="<input type=hidden name=c[renewal_duration] value=\"".$this->renew_upgrade_variables["renewal_duration"]."\">";
					}

					if (($user_credits) && ($this->renew_upgrade_variables["use_credit_for_renewal"]))
						$this->body .="</td>\n\t<td width=50% class=ad_cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->cost_of_ad)." ".$this->configuration_data['postcurrency']."  ".urldecode($this->messages[1616])." </td>\n\t</tr>\n";
					else
						$this->body .="</td>\n\t<td width=50% class=ad_cost_data_values>".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->cost_of_ad)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}

				if (($this->configuration_data['use_bolding_feature']) && ($this->price_plan->USE_BOLDING)
					 && (($this->renew_upgrade_variables["bolding"]) || ($this->renew_upgrade_variables["bolding_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[841])."</td>\n\t";
					if ($this->total == 0)
						$this->body .="<input type=hidden name=c[bolding] value=1>";
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->BOLDING_PRICE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				if (($this->configuration_data['use_better_placement_feature']) && ($this->price_plan->USE_BETTER_PLACEMENT)
					 && (($this->renew_upgrade_variables["better_placement"]) || ($this->renew_upgrade_variables["better_placement_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[842])."</td>\n\t";
					if ($this->total == 0)
						$this->body .="<input type=hidden name=c[better_placement] value=1>";
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->BETTER_PLACEMENT_CHARGE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				if (($this->configuration_data['use_featured_feature']) && ($this->price_plan->USE_FEATURED_ADS)
					 && (($this->renew_upgrade_variables["featured_ad"]) || ($this->renew_upgrade_variables["featured_ad_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[843])."</td>\n\t";
					if ($this->total == 0)
						$this->body .="<input type=hidden name=c[featured_ad] value=1>";
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				if (($this->configuration_data['use_featured_feature_2']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_2)
					 && (($this->renew_upgrade_variables["featured_ad_2"]) || ($this->renew_upgrade_variables["featured_ad_2_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[2280])."</td>\n\t";
					if ($this->total == 0)
						$this->body .="<input type=hidden name=c[featured_ad_2] value=1>";
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_2)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				if (($this->configuration_data['use_featured_feature_3']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_3)
					 && (($this->renew_upgrade_variables["featured_ad_3"]) || ($this->renew_upgrade_variables["featured_ad_3_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[2281])."</td>\n\t";
					if ($this->total == 0)
						$this->body .="<input type=hidden name=c[featured_ad_3] value=1>";
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_3)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				if (($this->configuration_data['use_featured_feature_4']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_4)
					 && (($this->renew_upgrade_variables["featured_ad_4"]) || ($this->renew_upgrade_variables["featured_ad_4_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[2282])."</td>\n\t";
					if ($this->total == 0)
						$this->body .="<input type=hidden name=c[featured_ad_4] value=1>";
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_4)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				if (($this->configuration_data['use_featured_feature_5']) && ($this->price_plan->USE_FEATURED_ADS_LEVEL_5)
					&& (($this->renew_upgrade_variables["featured_ad_5"]) || ($this->renew_upgrade_variables["featured_ad_5_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[2283])."</td>\n\t";
					if ($this->total == 0)
						$this->body .="<input type=hidden name=c[featured_ad_5] value=1>";
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->FEATURED_AD_PRICE_5)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				if (($this->configuration_data['use_attention_getters']) && ($this->price_plan->USE_ATTENTION_GETTERS)
					 && (($this->renew_upgrade_variables["attention_getter"]) || ($this->renew_upgrade_variables["attention_getter_upgrade"])))
				{
					$this->body .="<tr>\n\t<td  class=ad_cost_field_labels>
						".urldecode($this->messages[844])."</td>\n\t";
					if ($this->total == 0)
					{
						$this->body .="<input type=hidden name=c[";
						if ($this->renew_upgrade_variables["attention_getter_upgrade"])
							$this->body .= "attention_getter_upgrade";
						elseif ($this->renew_upgrade_variables["attention_getter"])
							$this->body .= "attention_getter";
						$this->body .="] value=1>";
					}
					$this->body .="<td class=ad_cost_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->price_plan->ATTENTION_GETTER_PRICE)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
				}
				//subtotal
				$this->body .="<tr>\n\t<td class=subtotal_field_label>
					".urldecode($this->messages[845])."</td>\n\t
					<td class=subtotal_data_value>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->subtotal)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

				//tax
				$this->body .="<tr >\n\t<td class=tax_field_label>
					".urldecode($this->messages[846])."</td>\n\t
					<td class=tax_field_value>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->tax)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

				//total
				$this->body .="<tr >\n\t<td class=total_field_label>
					".urldecode($this->messages[847])."</td>\n\t
					<td class=total_field_value>\n\t".$this->configuration_data['precurrency']." ".sprintf("%0.02f",$this->total)." ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";

				$this->body .="</table>\n\t</td>\n</tr>\n";

				$this->body .="<tr>\n\t<td align=center class=complete_transaction_button><input type=submit name=z value=\"".urldecode($this->messages[848])."\">\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td align=center colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=7&b=".$this->classified_id."&r=".$this->renew_upgrade." class=back_to_my_currents_ads_link>".urldecode($this->messages[863])."</a><br>";
				$this->body .="<tr>\n\t<td align=center colspan=2><a href=".trim($this->configuration_data['classifieds_url'])."?a=4&b=1 class=back_to_my_currents_ads_link>".urldecode($this->messages[849])."</a><br>";
				$this->body .="</td>\n</tr>\n";

				$this->body .="</table>\n";
				$this->body .="</form>\n";
				$this->display_page($db);
				return true;
			}
			else
			{
				//cannot upgrade or renew now
				return false;
			}
		}
		else
		{
			return false;
		}

	} //end of function final_approval_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function transaction_approved($db)
	{
		if ($this->debug_renewals) echo "<br>TOP OF TRANSACTION_APPROVED<br>\n";
		$this->get_cost_of_ad($db);
		$user_data = $this->get_user_data($db);
		$this->tax = $this->get_tax($db,$user_data);
		$this->total = $this->subtotal + $this->tax;
		if ($this->debug_renewals)
		{
			echo $this->cost_of_ad." is cost_of_ad in transaction_approved<br>\n";
			echo $this->tax." is the tax in transaction_approved<br>\n";
			echo $this->total." is the total in transaction_approved<bR>\n";
			echo $this->renew_upgrade_variables["item_type"].' is the item_type<Br>';
		}
		if($this->renew_upgrade_variables["item_type"] == 2)
		{
			//copy old auction data to new auction
			if(!$this->copy_old_auction_to_new($db))
			{
				if($this->debug_renewals)
					echo 'Error: '.$this->sql_query.'<Br>';
			}
		}

		$this->sql_query = "update ".$this->classifieds_table." set
			customer_approved = 1
			where id = ".$this->classified_id;
		$transaction_result = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
		if (!$transaction_result)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
		if ($this->total == 0)
		{
			//there is no charge to place this ad
			//make it live now
			if ($this->debug_renewals)
				echo "doing free renewals<bR>\n";
			if (!$this->do_free_upgrades_and_renewals($db))
				return false;
		}
		else
		{
			switch ($this->renew_upgrade_variables["payment_type"])
			{
				case 1:
				{
					//cash
					//administrator must open classified ad
					//check if instant renewal or placed on hold
					//place in renewal transactions expecting funds or on hold
					$this->page_id = 58;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[850])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[851])."</td>\n</tr>\n";

					if ($this->price_plan->INSTANT_CASH_RENEWALS)
					{
						$this->renew_length_by_session($db);
						$this->renew_upgrade_by_session($db);
						//display message saying the ad has been renewed but expecting payment
						$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1608])."</td>\n</tr>\n";
					}
					else
					{
						//display message saying the ad has not been renewed until payment
						$this->body .= "<tr class=success_failure_message><td>".$classified_id." ~ ".urldecode($this->messages[1609])." ".
							$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->total).
							" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
					}

					$this->remove_renew_upgrade_session($db,$this->session_id);
					$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=1\" class=my_account_link >";
					$this->body .=urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->update_renewal_payment_expected($db,$this->classified_id);
					$this->display_page($db);
					exit;

				}
				break;
				case 2:
				{
					//credit card
					//each credit card processor will have its own transaction handler
					//find the right credit card processor
					if ($this->debug_renewals)
						echo "hello from credit card<bR>\n";
					$user_data = $this->get_user_data($db,$this->classified_user_id);
					$this->update_renewal_payment_expected($db,$this->classified_id);
					$cc = $this->get_cc($db);
					if ($cc)
					{
						include($cc->CC_INITIATE_FILE);
						return false;
						exit;
					}
					else
					{
						return true;
					}
				}
				break;
				case 3:
				{
					//paypal
					//paypal will have a separate final transaction handler that opens the classified ad
					//get unique verifier for paypal 'custom' field
					$this->update_renewal_payment_expected($db,$this->classified_id);
					include("paypal_initiate.php");
				}
				break;
				case 4:
				{
					//money order
					//administrator must open classified ad
					//check if instant renewal or placed on hold
					$this->page_id = 58;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[850])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[851])."</td>\n</tr>\n";

					if ($this->price_plan->INSTANT_MONEY_ORDER_RENEWALS)
					{
						$this->renew_length_by_session($db);
						$this->renew_upgrade_by_session($db);
						//display message saying the ad has been renewed but expecting payment
						$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1610])."</td>\n</tr>\n";
					}
					else
					{
						//display message saying the ad has not been renewed until payment
						$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1611])." ".$classified_id." ~ ".
							$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->total).
							" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
					}

					$this->remove_renew_upgrade_session($db,$this->session_id);
					$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=1\" class=my_account_link >";
					$this->body .=urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->update_renewal_payment_expected($db,$this->classified_id);
					$this->display_page($db);
					exit;
				}
				break;
				case 5:
				{
					//check
					//administrator must open classified ad
					//check if instant renewal or placed on hold
					$this->page_id = 58;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[850])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[851])."</td>\n</tr>\n";

					if ($this->price_plan->INSTANT_CHECK_RENEWALS)
					{
						$this->renew_length_by_session($db);
						$this->renew_upgrade_by_session($db);
						//display message saying the ad has been renewed but expecting payment
						$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1612])."</td>\n</tr>\n";
					}
					else
					{
						//display message saying the ad has not been renewed until payment
						$this->body .= "<tr class=success_failure_message><td>".urldecode($this->messages[1613])." ".$classified_id." ~ ".
							$this->configuration_data['precurrency']." ".sprintf("%01.2f",$this->total).
							" ".$this->configuration_data['postcurrency']."</td>\n</tr>\n";
					}
					$this->remove_renew_upgrade_session($db,$this->session_id);
					$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=1\" class=my_account_link >";
					$this->body .=urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					$this->update_renewal_payment_expected($db,$this->classified_id);
					$this->display_page($db);
					exit;
				}
				break;
				case 6:
				{
					//worldpay
					$this->update_renewal_payment_expected($db,$this->classified_id);
					include("initiate_worldpay.php");
					return true;
				}
				break;
				case 7:
				{
					//site balance / invoice payment choice
					$this->page_id = 58;
					$this->get_text($db);
					$this->body .= "<table width=100% border=0 cellpadding=1 cellspacing=1>\n\t";
					$this->body .= "<tr class=page_title><td>".urldecode($this->messages[850])."</td>\n</tr>\n";
					$this->body .= "<tr class=page_description><td>".urldecode($this->messages[851])."</td>\n</tr>\n";

					if (($this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
					{
						$new_balance = $this->user_data->ACCOUNT_BALANCE - $this->total;
						$this->sql_query = "update ".$this->userdata_table." set
							account_balance = ".$new_balance."
							where id = ".$this->classified_user_id;
						if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
						$update_balance_results = $db->Execute($this->sql_query);
						if (!$update_balance_results)
						{
							if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
							return false;
						}

						$this->sql_query = "insert into ".$this->balance_transactions."
							(user_id,ad_id,amount,date,cc_transaction_id,invoice_id,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,
							featured_ad_4,featured_ad_5,attention_getter,renewal)
							values
							(".$this->classified_user_id.",".$this->classified_id.",".$this->total.",".$this->shifted_time($db).",999999999,999999999,".
							$this->renew_upgrade_variables["bolding"].",".$this->renew_upgrade_variables["better_placement"].",".
							$this->renew_upgrade_variables["featured_ad"].",".$this->renew_upgrade_variables["featured_ad_2"].",".
							$this->renew_upgrade_variables["featured_ad_3"].",".$this->renew_upgrade_variables["featured_ad_4"].",".
							$this->renew_upgrade_variables["featured_ad_5"].",".$this->renew_upgrade_variables["attention_getter"].",".
							$this->renew_upgrade.")";
						if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
						$insert_invoice_item_result = $db->Execute($this->sql_query);
						if (!$insert_invoice_item_result)
						{
							if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
							return false;
						}
					}
					elseif ((!$this->configuration_data['positive_balances_only']) && ($this->configuration_data['use_account_balance']))
					{
						$this->sql_query = "insert into ".$this->balance_transactions."
							(user_id,ad_id,amount,date,bolding,better_placement,featured_ad,featured_ad_2,featured_ad_3,
							featured_ad_4,featured_ad_5,attention_getter,renewal)
							values
							(".$this->classified_user_id.",".$this->classified_id.",".$this->total.",".$this->shifted_time($db).",".$this->renew_upgrade_variables["bolding"].",
							".$this->renew_upgrade_variables["better_placement"].",".$this->renew_upgrade_variables["featured_ad"].",
							".$this->renew_upgrade_variables["featured_ad_2"].",".$this->renew_upgrade_variables["featured_ad_3"].",
							".$this->renew_upgrade_variables["featured_ad_4"].",".$this->renew_upgrade_variables["featured_ad_5"].",
							".$this->renew_upgrade_variables["attention_getter"].",".
							$this->renew_upgrade.")";
						if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
						$insert_invoice_item_result = $db->Execute($this->sql_query);
						if (!$insert_invoice_item_result)
						{
							if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
							return false;
						}
					}

					$this->renew_length_by_session($db);
					$this->renew_upgrade_by_session($db);
					//display message that ad has been renewed and will be billed to account balance or invoiced
					$this->body .="<tr>\n\t<td class=success_failure_message >".urldecode($this->messages[3189])."</td>\n</tr>\n";

					$this->remove_renew_upgrade_session($db,$this->session_id);
					$this->body .="<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=1\" class=my_account_link >";
					$this->body .=urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					//$this->update_renewal_payment_expected($db,$this->classified_id);
					$this->display_page($db);
					exit;
				}
				break;
				case 8:
				{
					//NOCHEX
					$this->update_renewal_payment_expected($db,$this->classified_id);
					include("nochex_initiate.php");
					return true;
				}
				break;
				default:
					if ($this->debug_renewals) echo "got to default in transaction_approved<br>";
					return false;
			} //end of switch ($this->renew_upgrade_variables["payment_type"])
		}

		// Update category count
		$this->sql_query = "select category from ".$this->classifieds_table." where id = ".$this->classified_id;
		$result = $db->Execute($this->sql_query);
		if(!$result)
		{
			return false;
		}
		else
		{
			$category_id = $result->FetchNextObject();
		}

		$this->sql_query = "select category_count from ".$this->categories_table." where ".$category_id->CATEGORY_ID;
		$result = $db->Execute($this->sql_query);
		if(!$result)
		{
			return false;
		}
		else
		{
			$category_count = $result->FetchNextObject();
		}

		$this->sql_query = "update ".$this->categories_table." set category_count = ".$category_count->CATEGORY_COUNT." where category_id = ".$category_id->CATEGORY_ID;
		$result = $db->Execute($this->sql_query);
		if(!$result)
		{
			return false;
		}
		return true;
	} //end of function transaction_approved

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_price_plan($db)
	{
		//get price plan specifics
		if ($this->price_plan_id)
		{
			$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$this->price_plan_id;
			$price_plan_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$price_plan_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			elseif ($price_plan_result->RecordCount() == 1)
			{
				$show = $price_plan_result->FetchNextObject();
				return $show;
			}
			else
			{
				//echo "no price return<br>\n";
				return false;
			}
		}
		else
		{
			//echo "no price plan id<Br>\n";
			return false;
		}

	} //end of function get_price_plan

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cost_of_ad($db)
	{
		if ($this->debug_renewals)
		{
			echo "<br>TOP OF GET_COST_OF_AD<br>\n";
		}
		$price_plan = $this->get_this_price_plan($db);
		if ($price_plan)
		{
			if ($this->debug_renewals)
			{
				echo $price_plan->TYPE_OF_BILLING." is type of billing in get_cost_of_ad<br>\n";
				echo $this->renew_upgrade." is renew_upgrade in get_cost_of_ad<br>\n";
			}
			if ($this->renew_upgrade == 1)
			{
				if ($price_plan->TYPE_OF_BILLING == 1)
				{
					if (($this->renew_upgrade_variables["use_credit_for_renewal"]) && ($this->user_credits))
					{
						if ($this->debug_renewals)
						{
							//cost of ad is 0 - using a credit
							echo "cost of ad is 0 in get_cost_of_ad<br>";
						}
						$this->cost_of_ad = 0;
					}
					else
					{
						if ($this->debug_renewals)
						{
							echo $price_plan->CHARGE_PER_AD_TYPE." is charge per ad type in get_cost_of_ad<bR>\n";
						}
						switch ($price_plan->CHARGE_PER_AD_TYPE)
						{
							case 1: //get the charge based on the price field
								if ($price_plan->CATEGORY_ID)
									$this->sql_query = "select renewal_charge from ".$this->price_plans_increments_table." where".
										"((low<=\"".$this->classified_data->PRICE."\" AND high>=\"".$this->classified_data->PRICE."\") OR".
										"(low<\"".$this->classified_data->PRICE."\" AND high<\"".$this->classified_data->PRICE."\")) and
										price_plan_id = ".$this->price_plan_id." and category_id = ".$price_plan->CATEGORY_ID." ORDER BY charge DESC limit 1";
								else
									$this->sql_query = "select renewal_charge from ".$this->price_plans_increments_table." where".
										"((low<=\"".$this->classified_data->PRICE."\" AND high>=\"".$this->classified_data->PRICE."\") OR".
										"(low<\"".$this->classified_data->PRICE."\" AND high<\"".$this->classified_data->PRICE."\"))
										and price_plan_id = ".$this->price_plan_id." and category_id = 0 ORDER BY charge DESC limit 1";
								if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
								$increment_result = $db->Execute($this->sql_query);
								if (!$increment_result)
								{
									if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
									$this->cost_of_ad = $price_plan->AD_RENEWAL_COST;
								}
								elseif  ($increment_result->RecordCount() == 1)
								{
									$show_increment = $increment_result->FetchNextObject();
									$this->cost_of_ad = $show_increment->RENEWAL_CHARGE;
								}
								else
								{
									$this->cost_of_ad = $price_plan->AD_RENEWAL_COST;
								}
								break;
							case 2: //get the charge based on price range charge
								if ($this->renew_upgrade_variables["renewal_length"])
								{
									if ($price_plan->CATEGORY_ID)
										$this->sql_query = "select renewal_charge from ".$this->price_plan_lengths_table."
											where length_of_ad = ".$this->renew_upgrade_variables["renewal_length"]."
											and price_plan_id = ".$this->price_plan_id." and category_id = ".$price_plan->CATEGORY_ID;
									else
										$this->sql_query = "select renewal_charge from ".$this->price_plan_lengths_table."
											where length_of_ad = ".$this->renew_upgrade_variables["renewal_length"]."
											and price_plan_id = ".$this->price_plan_id." and category_id = 0";

									if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
									$length_result = $db->Execute($this->sql_query);
									if (!$length_result)
									{
										if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
										$this->cost_of_ad = $price_plan->AD_RENEWAL_COST;
									}
									elseif  ($length_result->RecordCount() == 1)
									{
										$show_length_cost = $length_result->FetchNextObject();
										$this->cost_of_ad = $show_length_cost->RENEWAL_CHARGE;
									}
									else
									{
										$this->cost_of_ad = $price_plan->AD_RENEWAL_COST;
									}
								}
								else
								{
									$this->cost_of_ad = 0;
								}
								break;

							default:
							{
								if ($this->debug_renewals)
								{
									echo $ad_renewal." is whether to ad renew or not<BR>\n";
									echo "defaulted to ".$price_plan->AD_RENEWAL_COST."<Br>\n";
									echo $this->renew_upgrade_variables["ad_renewal"]." is ad_renewal in get_cost_of_ad<bR>\n";
									echo $this->renew_upgrade_variables["renewal_length"]." is ad_renewal_length in get_cost_of_ad<bR>\n";
								}
								//if (($this->renew_upgrade_variables["ad_renewal"]) && ($this->renew_upgrade_variables["renewal_length"]))
								$this->cost_of_ad = $price_plan->AD_RENEWAL_COST;
								break;
							}

						} //end of switch
					}
				}
				elseif ($price_plan->TYPE_OF_BILLING == 2)
				{
					//CANNOT RENEW WHILE ON SUBSCRIPTION BASED PLAN
				}
				else
				{
					return false;
				}
				//$this->body .=$this->cost_of_ad." is the subtotal cost of ad in get cost of ad<Br>\n";
				$this->subtotal = $this->cost_of_ad;
				if ($this->debug_renewals)
					echo $this->subtotal." is the subtotal in get_cost_of_ad<br>\n";

				if (($this->configuration_data['use_bolding_feature']) && ($this->renew_upgrade_variables["bolding"]))
					$this->subtotal  = $this->subtotal + $price_plan->BOLDING_PRICE;
				if (($this->configuration_data['use_better_placement_feature']) && ($this->renew_upgrade_variables["better_placement"]))
					$this->subtotal  = $this->subtotal  + $price_plan->BETTER_PLACEMENT_CHARGE;
				if (($this->configuration_data['use_featured_feature']) && ($this->renew_upgrade_variables["featured_ad"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE;
				if (($this->configuration_data['use_featured_feature_2']) && ($this->renew_upgrade_variables["featured_ad_2"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_2;
				if (($this->configuration_data['use_featured_feature_3']) && ($this->renew_upgrade_variables["featured_ad_3"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_3;
				if (($this->configuration_data['use_featured_feature_4']) && ($this->renew_upgrade_variables["featured_ad_4"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_4;
				if (($this->configuration_data['use_featured_feature_5']) && ($this->renew_upgrade_variables["featured_ad_5"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_5;

				if (($this->configuration_data['use_attention_getters']) && ($this->renew_upgrade_variables["attention_getter"]))
					$this->subtotal  = $this->subtotal  + $price_plan->ATTENTION_GETTER_PRICE;
				if ($this->debug_renewals)
					echo $this->subtotal." is the subtotal 2 in get_cost_of_ad<br>\n";
			}
			elseif ($this->renew_upgrade == 2)
			{
				$this->subtotal = 0;
				if (($this->configuration_data['use_bolding_feature']) && ($this->renew_upgrade_variables["bolding_upgrade"]))
					$this->subtotal  = $this->subtotal + $price_plan->BOLDING_PRICE;

				if (($this->configuration_data['use_better_placement_feature']) && ($this->renew_upgrade_variables["better_placement_upgrade"]))
					$this->subtotal  = $this->subtotal  + $price_plan->BETTER_PLACEMENT_CHARGE;

				if (($this->configuration_data['use_featured_feature']) && ($this->renew_upgrade_variables["featured_ad_upgrade"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE;
				if (($this->configuration_data['use_featured_feature_2']) && ($this->renew_upgrade_variables["featured_ad_2_upgrade"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_2;
				if (($this->configuration_data['use_featured_feature_3']) && ($this->renew_upgrade_variables["featured_ad_3_upgrade"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_3;
				if (($this->configuration_data['use_featured_feature_4']) && ($this->renew_upgrade_variables["featured_ad_4_upgrade"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_4;
				if (($this->configuration_data['use_featured_feature_5']) && ($this->renew_upgrade_variables["featured_ad_5_upgrade"]))
					$this->subtotal  = $this->subtotal  + $price_plan->FEATURED_AD_PRICE_5;
				if (($this->configuration_data['use_attention_getters']) && ($this->renew_upgrade_variables["attention_getter_upgrade"]))
					$this->subtotal  = $this->subtotal  + $price_plan->ATTENTION_GETTER_PRICE;
			}
			if ($this->debug_renewals)
				echo $this->subtotal." is subtotal in get_cost_of_ad where renew_upgrade = ".$this->renew_upgrade."<br>\n";
			if ($this->debug_renewals)
			{
				echo "<br>BOTTOM OF GET_COST_OF_AD<br>\n";
			}
			return true;
		}
		else
		{
			if ($this->debug_renewals)
			{
				echo "<br>BOTTOM OF GET_COST_OF_AD<br>\n";
			}
			return false;
		}
	} // end of function get_cost_of_ad

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_this_price_plan($db)
	{
		if ($this->debug_renewals)
		{
			echo "<br>TOP  OF GET_THIS_PRICE_PLAN<br>\n";
		}
		$base_price_plan = $this->get_price_plan($db,$this->price_plan_id);
		if ($base_price_plan->TYPE_OF_BILLING == 1)
		{
			if ($this->category_id)
			{
				$category_id = $this->category_id;
				do {
					$this->sql_query = "select * from ".$this->price_plans_categories_table." where
						price_plan_id = ".$this->price_plan_id." and category_id = ".$category_id;
					$category_price_plan_result = $db->Execute($this->sql_query);
					if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
					if (!$category_price_plan_result)
					{
						if ($this->debug_renewals) echo $this->sql_query." - HAS AN ERROR<bR>\n";
						return false;
					}
					elseif ($category_price_plan_result->RecordCount() == 1)
					{
						if ($this->debug_renewals)
						{
							echo "category price plan found for category: ".$category_id."<bR>\n";
						}
						$show_price_plan->CATEGORY_ID = $category_id;
						$show_price_plan = $category_price_plan_result->FetchNextObject();
						break;
					}
					else
						$show_price_plan = 0;
					if (!$show_price_plan)
					{
						//get category parent
						$this->sql_query = "select parent_id from ".$this->categories_table." where category_id = ".$category_id;
						$category_result = $db->Execute($this->sql_query);
						if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
						if (!$category_result)
						{
							if ($this->debug_renewals) echo $this->sql_query." - HAS AN ERROR<bR>\n";
							return false;
						}
						elseif ($category_result->RecordCount() == 1)
						{
							$show_category = $category_result->FetchNextObject();
							$category_id = $show_category->PARENT_ID;
						}
						else
							return false;
					}
					//check all the way to the main category
				} while ($category_id != 0);

				if (!$show_price_plan)
				{
					$this->price_plan = $base_price_plan;
					if ($this->debug_renewals)
					{
						echo "using base price plan<BR>\n";
						echo "BOTTOM OF GET_THIS_PRICE_PLAN<BR><br>\n";
					}
					return $base_price_plan;
				}
				else
				{
					$show_price_plan->TYPE_OF_BILLING = $base_price_plan->TYPE_OF_BILLING;
					$show_price_plan->MAX_ADS_ALLOWED = $base_price_plan->MAX_ADS_ALLOWED;
					//$show_price_plan->CHARGE_PER_AD_TYPE = $base_price_plan->CHARGE_PER_AD_TYPE;
					if ($this->debug_renewals)
					{
						echo $show_price_plan->TYPE_OF_BILLING." is type of billing set<BR>";
						echo $show_price_plan->MAX_ADS_ALLOWED." is MAX_ADS_ALLOWED set<BR>";
						echo $show_price_plan->CHARGE_PER_AD_TYPE." is CHARGE_PER_AD_TYPE set<BR>";
					}
					$this->price_plan = $show_price_plan;
					if ($this->debug_renewals)
					{
						echo "BOTTOM OF GET_THIS_PRICE_PLAN<BR><br>\n";
					}
					return $show_price_plan;
				}
			}
		}
		elseif ($base_price_plan->TYPE_OF_BILLING == 2)
		{
			//subscription based
			$this->price_plan = $base_price_plan;
			if ($this->debug_renewals)
			{
				echo "BOTTOM OF GET_THIS_PRICE_PLAN<BR><br>\n";
			}
			return $base_price_plan;
		}
		else
		{
			if ($this->debug_renewals)
			{
				echo "BOTTOM OF GET_THIS_PRICE_PLAN<BR><br>\n";
			}
			return false;
		}
	} //end of function get_this_price_plan

//#####################################################################

	function display_basic_duration_dropdown($db)
	{
		$this->sql_query = "select * from ".$this->choices_table." where type_of_choice = 1 order by numeric_value";
		$duration_result = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
		if (!$duration_result)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
		elseif ($duration_result->RecordCount() > 0)
		{
			while ($show_durations = $duration_result->FetchNextObject())
			{
				$this->body .= "<option value=".$show_durations->NUMERIC_VALUE;
				if ($this->renew_upgrade_variables["renewal_length"] == $show_durations->NUMERIC_VALUE)
					   $this->body .= " selected";
				$this->body .= ">".$show_durations->DISPLAY_VALUE;
				$this->body .= " ".urldecode($this->messages[546])."</option>";
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function display_basic_duration_dropdown

//#####################################################################

	function do_free_upgrades_and_renewals($db)
	{
		if ($this->debug_renewals) echo "TOP OF DO_FREE_UPGRADES_AND_RENEWALS<br>\n";
		$this->page_id = 58;
		$this->get_text($db);
		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=page_title>\n\t<td>".urldecode($this->messages[850])."</td>\n</tr>\n";
		$this->body .="<tr class=page_description>\n\t<td>".urldecode($this->messages[851])."</td>\n</tr>\n";
		if ($this->debug_renewals)
		{
			echo $this->renew_upgrade." is renew_upgrade in do_free_upgrades_and_renewals<br>\n";
			echo $this->price_plan." is price_plan in do_free_upgrades_and_renewals<br>\n";
			echo $this->price_plan_id." is price_plan_id in do_free_upgrades_and_renewals<br>\n";
			echo $this->classified_data." is classified_data in do_free_upgrades_and_renewals<br>\n";
			echo $this->category_id." is category_id in do_free_upgrades_and_renewals<br>\n";
			echo $this->classified_id." is classified_id in do_free_upgrades_and_renewals<br>\n";
			echo $this->classified_user_id." is classified_user_id in do_free_upgrades_and_renewals<br>\n";
		}

		if (($this->classified_id) && ($this->classified_user_id) && ($this->category_id) && ($this->price_plan_id))
		{
			//echo "doing the free stuff<br>\n";
			if ($this->renew_upgrade == 1)
			{
				$renew_cutoff = ($this->classified_data->ENDS - ($this->configuration_data['days_to_renew'] * 86400));
				$renew_postcutoff = ($this->classified_data->ENDS + ($this->configuration_data['days_to_renew'] * 86400));
				if ($this->debug_renewals)
				{
					echo $this->configuration_data['days_to_renew']." is days to renew  in do_free_upgrades_and_renewals<Br>\n";
					echo $renew_cutoff." - ".$this->shifted_time($db)." - ".$renew_postcutoff."  in do_free_upgrades_and_renewals<br>\n";
					echo $this->renew_upgrade_variables["ad_renewal"]." is ad_renewal  in do_free_upgrades_and_renewals<bR>\n";
					echo $this->price_plan->AD_RENEWAL_COST." is price_plan->AD_RENEWAL_COST  in do_free_upgrades_and_renewals<br>\n";
					echo $this->subtotal." is subtotal in do_free_upgrades_and_renewals<Br>\n";
					echo $this->user_credits." is user_credits  in do_free_upgrades_and_renewals<BR>\n";
					echo $this->renew_upgrade_variables["use_credit_for_renewal"]." is renew_upgrade_variables[use_credit_for_renewal]  in do_free_upgrades_and_renewals<br>\n";
				}

				if (($this->configuration_data['days_to_renew']) && ($this->shifted_time($db) > $renew_cutoff) && ($this->shifted_time($db) < $renew_postcutoff))
				{
					$this->renew_length_by_session($db);
					$this->renew_upgrade_by_session($db);

				}
			}

			if ($this->renew_upgrade == 2)
			{
				$upgrade_cutoff = ($this->classified_data->DATE + ($this->configuration_data['days_can_upgrade'] * 86400));
				if (($this->configuration_data['days_can_upgrade']) && ($this->shifted_time($db) < $upgrade_cutoff))
				{
					$this->renew_upgrade_by_session($db);


					$this->sql_query = "select date, ends from ".$this->classifieds_table." where id = ".$this->classified_id;
					$result = $db->Execute($this->sql_query);
					if(!$result)
						return false;
					else
						$show_classified = $result->FetchNextObject();

					// Change end time
					if($this->configuration_data['upgrade_time'])
					{
						$end_time = ($show_classified->ENDS - $show_classified->DATE) + $this->shifted_time($db);
						$this->sql_query = "update geodesic_classifieds set
							ends = ".$end_time."
							where id = ".$this->classified_id;
						$end_result = $db->Execute($this->sql_query);
					}
				}
			}
			$this->body .= "<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[852])."</td>\n</tr>\n";
		}
		else
		{
			$this->body .="<tr>\n\t<td class=success_failure_message>
				".urldecode($this->messages[857])."</td>\n</tr>\n";
		}

		$this->body .= "<tr class=my_account_link >\n\t<td><A HREF=\"".$this->configuration_data['classifieds_file_name']."?a=4&b=1\" class=my_account_link >";
		$this->body .= urldecode($this->messages[860])."</A>\n\t</td>\n</tr>\n";
		$this->remove_renew_upgrade_session($db,$this->session_id);
		$this->update_category_count($db,$this->category_id);
		$this->body .= "</table>\n";
		$this->display_page($db);
		return true;
	} //end of function do_free_upgrades_and_renewals

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_cc($db)
	{
		$this->sql_query = "select * from ".$this->cc_choices." where chosen_cc = 1";

		$cc_result = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
		if (!$cc_result)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
		elseif ($cc_result->RecordCount() == 1)
		{
			$show = $cc_result->FetchNextObject();
			return $show;
		}
		else
		{
			return false;
		}

	} //end of function get_cc

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_renewal_payment_expected($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				renewal_length = ".$this->renew_upgrade_variables["renewal_length"].",
				renewal_featured_ad = ".$this->renew_upgrade_variables["featured_ad"].",
				renewal_featured_ad_2 = ".$this->renew_upgrade_variables["featured_ad_2"].",
				renewal_featured_ad_3 = ".$this->renew_upgrade_variables["featured_ad_3"].",
				renewal_featured_ad_4 = ".$this->renew_upgrade_variables["featured_ad_4"].",
				renewal_featured_ad_5 = ".$this->renew_upgrade_variables["featured_ad_5"].",
				renewal_bolding = ".$this->renew_upgrade_variables["bolding"].",
				renewal_better_placement = ".$this->renew_upgrade_variables["better_placement"].",
				renewal_attention_getter = ".$this->renew_upgrade_variables["attention_getter"].",
				renewal_attention_getter_choice = ".$this->renew_upgrade_variables["attention_getter_choice"].",
				featured_ad_upgrade = ".$this->renew_upgrade_variables["featured_ad_upgrade"].",
				featured_ad_2_upgrade = ".$this->renew_upgrade_variables["featured_ad_2_upgrade"].",
				featured_ad_3_upgrade = ".$this->renew_upgrade_variables["featured_ad_3_upgrade"].",
				featured_ad_4_upgrade = ".$this->renew_upgrade_variables["featured_ad_4_upgrade"].",
				featured_ad_5_upgrade = ".$this->renew_upgrade_variables["featured_ad_5_upgrade"].",
				bolding_upgrade = ".$this->renew_upgrade_variables["bolding_upgrade"].",
				better_placement_upgrade = ".$this->renew_upgrade_variables["better_placement_upgrade"].",
				attention_getter_upgrade = ".$this->renew_upgrade_variables["attention_getter_upgrade"].",
				attention_getter_choice_upgrade = ".$this->renew_upgrade_variables["attention_getter_choice_upgrade"].",
				renewal_payment_expected = ".$this->renew_upgrade.",
				renewal_payment_expected_by = ".$this->classified_data->ENDS.",
				renewal_total = ".$this->total.",
				renewal_subtotal = ".$this->subtotal.",
				renewal_tax = 0
				where id = ".$classified_id;
			$update_renewal_payment_expected_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$update_renewal_payment_expected_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}

			return true;
		}
		else
			return false;
	} //end of function update_renewal_payment_expected

//####################################################################

	function get_user_credits($db)
	{
		//expire user credits
		$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$this->classified_user_id." order by credits_expire asc limit 1";
		$credits_results = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";

		if (!$credits_results)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
		elseif ($credits_results->RecordCount() == 1)
		{
			$this->user_credits = 1;
		}
		else
		{
			$this->user_credits = 0;
		}
	} //end of function get_user_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function expire_credits($db)
	{
		//expire user credits
		$this->sql_query = "delete from ".$this->user_credits_table." where credits_expire < ".$this->shifted_time($db)." or credit_count = 0";
		$expire_results = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
		if (!$expire_results)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
		else
		{
			return true;
		}
	} //end of function expire_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function remove_a_users_credit($db)
	{
		$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$this->classified_user_id." order by credits_expire asc limit 1";
		$credits_results = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
		if (!$credits_results)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
		elseif ($credits_results->RecordCount() == 1)
		{
			//remove one of these credits
			$show_credits = $credits_results->FetchNextObject();
			if ($show_credits->CREDIT_COUNT == 1)
			{
				//delete from the table
				$this->sql_query = "delete from ".$this->user_credits_table." where
					credits_id = ".$show_credits->CREDITS_ID."
					and user_id = ".$this->classified_user_id;
				$remove_credits_results = $db->Execute($this->sql_query);
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				if (!$remove_credits_results)
				{
					if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
					return false;
				}
			}
			else
			{
				//remove one from the credit count
				$this->sql_query = "update ".$this->user_credits_table." set
					credit_count = ".($show_credits->CREDIT_COUNT - 1)."
					where credits_id = ".$show_credits->CREDITS_ID."
					and user_id = ".$this->classified_user_id;
				$remove_credit = $db->Execute($this->sql_query);
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				if (!$remove_credit)
				{
					if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
					return false;
				}
			}
		}
		return true;

	} //end of function remove_a_users_credit

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_maximum_ad_limit($db)
	{
		if ($this->classified_user_id)
		{
			if (($this->renew_upgrade ==1) && ($this->classified_data->ENDS > $this->shifted_time($db)))
			{
				//check to see if this user has reached their maximum ad count
				$this->sql_query = "select count(*) as total_ads from ".$this->classifieds_table." where seller = ".$this->classified_user_id." and live = 1";
				$total_ads_result = $db->Execute($this->sql_query);
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				if (!$total_ads_result)
				{
					if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
					$this->setup_error = $this->messages[86];
					return false;
				}
				elseif ($total_ads_result->RecordCount() == 1)
				{
					$show_total_ads = $total_ads_result->FetchNextObject();

					if ($show_total_ads->TOTAL_ADS <= $this->price_plan->MAX_ADS_ALLOWED)
						$this->max_ads_reached = 0;
					else
						$this->max_ads_reached = 1;
					return true;
				}
				else
				{
					$this->setup_error = $this->messages[86];
					return false;
				}
			}
			else
				$this->max_ads_reached = 0;
				return true;
		}
		else
		{
			$this->setup_error = $this->messages[86];
			return false;
		}
	} //end of function check_maximum_ad_limit

//#########################################################################

	function check_user_subscription($db)
	{
		$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire > ".$this->shifted_time($db)." and user_id = ".$this->classified_user_id;
		$get_subscriptions_results = $db->Execute($this->sql_query);
		if ($this->debug_renewals) echo $this->sql_query."<bR>\n";

		if (!$get_subscriptions_results)
		{
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			return false;
		}
		elseif ($get_subscriptions_results->RecordCount() == 0)
		{
			$this->user_currently_subscribed = 0;
			return true;
		}
		elseif ($get_subscriptions_results->RecordCount() > 0)
		{
			$this->user_currently_subscribed = 1;
			return true;
		}
	} // end of function check_user_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function copy_old_auction_to_new($db)
	{
		if ($this->renew_upgrade == 1)
		{
			//this is a renewal and not an upgrade
			//if renewal copy the auction to a new auction id along with all of its data
			//this will leave all old auction data intact
			$old_auction = $this->get_classified_data($db,$this->classified_id);

			//create sql insert statement
			$auction_length = $this->renew_upgrade_variables["renewal_length"] * 86400;
			$this->sql_query = "INSERT INTO ".$this->classifieds_table."
				(seller,live,type,title,auction_length,date,description,precurrency,postcurrency,image,category,
				location_city,location_state,location_country,location_zip,ends,search_text,business_type,
				optional_field_1,optional_field_2,optional_field_3,optional_field_4,
				optional_field_5,optional_field_6,optional_field_7,optional_field_8,
				optional_field_9,optional_field_10,optional_field_11,optional_field_12,
				optional_field_13,optional_field_14,optional_field_15,optional_field_16,
				optional_field_17,optional_field_18,optional_field_19,optional_field_20,
				email,expose_email,phone,phone2,fax,filter_id,mapping_address,mapping_city,mapping_state,
				mapping_country,mapping_zip,subscription_choice,minimum_bid,starting_bid,reserve_price,auction_type,
				quantity,buy_now,final_fee,url_link_1,url_link_2,url_link_3, payment_options,end_time, start_time,price_plan_id,
				transaction_type,cc_transaction_type, item_type)
				VALUES
				(\"".$this->classified_user_id."\",
				0,
				\"".$old_auction->TYPE."\",
				\"".$old_auction->TITLE."\",
				\"".$this->renew_upgrade_variables["renewal_length"]."\",
				\"".$this->shifted_time($db)."\",
				\"".$old_auction->DESCRIPTION."\",
				\"".$old_auction->PRECURRENCY."\",
				\"".$old_auction->POSTCURRENCY."\",
				\"".$old_auction->IMAGE."\",
				".$old_auction->CATEGORY.",
				\"".$old_auction->LOCATION_CITY."\",
				\"".$old_auction->LOCATION_STATE."\",
				\"".$old_auction->LOCATION_COUNTRY."\",
				\"".$old_auction->LOCATION_ZIP."\",
				\"".$this->shifted_time($db)."\",
				\"".$this->user_data->SEARCH_TEXT."\",
				\"".$this->user_data->BUSINESS_TYPE."\",
				\"".$old_auction->OPTIONAL_FIELD_1."\",
				\"".$old_auction->OPTIONAL_FIELD_2."\",
				\"".$old_auction->OPTIONAL_FIELD_3."\",
				\"".$old_auction->OPTIONAL_FIELD_4."\",
				\"".$old_auction->OPTIONAL_FIELD_5."\",
				\"".$old_auction->OPTIONAL_FIELD_6."\",
				\"".$old_auction->OPTIONAL_FIELD_7."\",
				\"".$old_auction->OPTIONAL_FIELD_8."\",
				\"".$old_auction->OPTIONAL_FIELD_9."\",
				\"".$old_auction->OPTIONAL_FIELD_10."\",
				\"".$old_auction->OPTIONAL_FIELD_11."\",
				\"".$old_auction->OPTIONAL_FIELD_12."\",
				\"".$old_auction->OPTIONAL_FIELD_13."\",
				\"".$old_auction->OPTIONAL_FIELD_14."\",
				\"".$old_auction->OPTIONAL_FIELD_15."\",
				\"".$old_auction->OPTIONAL_FIELD_16."\",
				\"".$old_auction->OPTIONAL_FIELD_17."\",
				\"".$old_auction->OPTIONAL_FIELD_18."\",
				\"".$old_auction->OPTIONAL_FIELD_19."\",
				\"".$old_auction->OPTIONAL_FIELD_20."\",
				\"".$old_auction->EMAIL."\",
				\"".$old_auction->EXPOSE_EMAIL."\",
				\"".$old_auction->PHONE."\",
				\"".$old_auction->PHONE2."\",
				\"".$old_auction->FAX."\",
				\"".$old_auction->FILTER_ID."\",
				\"".$old_auction->MAPPING_ADDRESS."\",
				\"".$old_auction->MAPPING_CITY."\",
				\"".$old_auction->MAPPING_STATE."\",
				\"".$old_auction->MAPPING_COUNTRY."\",
				\"".$old_auction->MAPPING_ZIP."\",
				\"".$old_auction->SUBSCRIPTION_CHOICE."\",
				\"".$old_auction->STARTING_BID."\",
				\"".$old_auction->STARTING_BID."\",
				\"".$old_auction->RESERVE_PRICE."\",
				\"".$old_auction->AUCTION_TYPE."\",
				\"".$old_auction->QUANTITY."\",
				\"".$old_auction->BUY_NOW."\",
				\"".$old_auction->FINAL_FEE."\",
				\"".$old_auction->URL_LINK_1."\",
				\"".$old_auction->URL_LINK_2."\",
				\"".$old_auction->URL_LINK_3."\",
				\"".$old_auction->PAYMENT_OPTIONS."\",
				\"".$this->shifted_time($db)."\",
				\"".$this->shifted_time($db)."\",
				\"".$old_auction->AUCTION_PRICE_PLAN_ID."\",
				\"".$old_auction->TRANSACTION_TYPE."\",
				\"".$old_auction->CC_TRANSACTION_TYPE."\",
				2)";

			$copy_result = $db->Execute($this->sql_query);
			if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
			if (!$copy_result)
			{
				if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
			}
			else
			{
				//get the insert id
				$new_auction_id = $db->Insert_ID();

				if ($this->debug_renewal) echo $new_auction_id." is the new_auction_id<Br>\n";

				//get images attached to auction
				//get images_urls attached to auction
				//get auction extras attached

				//delete the images
				$this->sql_query = "select * from ".$this->images_table." where classified_id = ".$this->classified_id;
				$get_image_result = $db->Execute($this->sql_query);
				if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
				if (!$get_image_result)
				{
					if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
					return false;
				}
				elseif ($get_image_result->RecordCount() > 0)
				{
					while ($show_image = $get_image_result->FetchNextObject())
					{
						$this->sql_query = "insert into ".$this->images_table."
							(filesize,filetype,filename,image_text,date_entered,classified_id,thumb,thumb_file,
							image_file,image_width,image_height,original_image_width,original_image_height,display_order)
							value
							(".$show_image->FILESIZE.",\"".$show_image->FILETYPE."\",\"".$show_image->FILENAME."\",
							,\"".$show_image->IMAGE_TEXT."\",\"".$this->shifted_time($db)."\",".$new_auction_id.",\"".$show_image->THUMB."\",
							,\"".$show_image->THUMB_FILE."\",\"".$show_image->IMAGE_FILE."\",
							,\"".$show_image->IMAGE_WIDTH."\",\"".$show_image->IMAGE_HEIGHT."\",
							,\"".$show_image->ORIGINAL_IMAGE_WIDTH."\",\"".$show_image->ORIGINAL_IMAGE_HEIGHT."\",
							\"".$show_image->DISPLAY_ORDER."\")";
						$insert_image_result = $db->Execute($this->sql_query);
						if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
						if (!$insert_image_result)
						{
							if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
							return false;
						}
					}
				}

				//delete from auctions extra questions
				$this->sql_query = "select * from ".$this->classified_extra_table." where classified_id = ".$this->classified_id;
				$get_extra_result = $db->Execute($this->sql_query);
				if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
				if (!$get_extra_result)
				{
					if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
					return false;
				}
				elseif ($get_extra_result->RecordCount() > 0)
				{
					while ($show_extra = $get_extra_result->FetchNextObject())
					{
						$this->sql_query = "insert into ".$this->classified_extra_table."
							(classified_id,name,question_id,value,explanation,checkbox,group_id,display_order)
							values
							(".$new_auction_id.",\"".$show_extra->NAME."\",\"".$show_extra->QUESTION_ID."\",
							\"".$show_extra->VALUE."\",\"".$show_extra->EXPLANATION."\",\"".$show_extra->CHECKBOX."\",
							\"".$show_extra->GROUP_ID."\",\"".$show_extra->DISPLAY_ORDER."\")";
						$insert_extra_results = $db->Execute($this->sql_query);
						if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
						if (!$insert_extra_results)
						{
							if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
							return false;
						}
					}
				}

				//delete url images
				//get image urls to
				$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$this->classified_id;
				$get_url_result = $db->Execute($this->sql_query);
				if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
				if (!$get_url_result)
				{
					if ($this->debug_renewal) echo $this->sql_query."<bR>\n";
					return false;
				}
				elseif ($get_url_result->RecordCount() > 0)
				{
					while ($show_url = $get_url_result->FetchNextObject())
					{
						if ($show_url->FULL_FILENAME)
						{
							//copy the full size image
							$image_dimensions = @getimagesize($show_url->FILE_PATH.$show_url->FULL_FILENAME);
							switch ($image_dimensions[2])
							{
								case 1: $extension = ".gif"; break;
								case 2: $extension = ".jpg"; break;
								case 3: $extension = ".png"; break;
								case 4: $extension = ".swf"; break;
								case 5: $extension = ".psd"; break;
								case 6: $extension = ".bmp"; break;
								case 7: $extension = ".tiff"; break;
								case 8: $extension = ".tiff"; break;
								case 9: $extension = ".jpc"; break;
								case 10: $extension = ".jp2"; break;
								case 11: $extension = ".jpx"; break;
								case 12: $extension = ".jb2"; break;
								case 13: $extension = ".swc"; break;
								case 14: $extension = ".iff"; break;
								case 15: $extension = ".wbmp"; break;
								case 16: $extension = ".xbm"; break;
								default: return false;
							}

							$this->sql_query = "select image_upload_path, url_image_directory from ".$this->ad_configuration_table;
							$result = $db->Execute($this->sql_query);

							if(!$result)
								return false;
							else
								$classified_configuration_data = $result->FetchNextObject();

							do {
								srand((double)microtime()*1000000);
								$filename_root = rand(1000000,9999999);
								$full_filepath = stripslashes($classified_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.$extension;
							} while (file_exists($full_filepath));

							$full_filename = $filename_root.$extension;
							if ($this->debug_renewal) echo $full_filepath." is the new filepath for new full image for ".$new_auction_id."<br>";

							if (!copy (($show_url->FILE_PATH.$show_url->FULL_FILENAME),$full_filepath))
							{
								$full_copied = 0;
								if ($this->debug_renewal) echo $full_filepath." was NOT copied<br>";
							}
							else
							{
								$full_copied = 1;
								if ($this->debug_renewal) echo $full_filepath." was copied<br>";
							}

						}

						if ($show_url->THUMB_FILENAME)
						{
							//copy the thumb image
							$image_dimensions = getimagesize($show_url->FILE_PATH.$show_url->THUMB_FILENAME);
							switch ($image_dimensions[2])
							{
								case 1: $extension = ".gif"; break;
								case 2: $extension = ".jpg"; break;
								case 3: $extension = ".png"; break;
								case 4: $extension = ".swf"; break;
								case 5: $extension = ".psd"; break;
								case 6: $extension = ".bmp"; break;
								case 7: $extension = ".tiff"; break;
								case 8: $extension = ".tiff"; break;
								case 9: $extension = ".jpc"; break;
								case 10: $extension = ".jp2"; break;
								case 11: $extension = ".jpx"; break;
								case 12: $extension = ".jb2"; break;
								case 13: $extension = ".swc"; break;
								case 14: $extension = ".iff"; break;
								case 15: $extension = ".wbmp"; break;
								case 16: $extension = ".xbm"; break;
								default: return false;
							}
							do {
								srand((double)microtime()*1000000);
								$filename_root = rand(1000000,9999999);
								$thumb_filepath = stripslashes($classified_configuration_data->IMAGE_UPLOAD_PATH).$filename_root.$extension;
							} while (file_exists($thumb_filepath));

							$thumb_filename = $filename_root.$extension;
							if ($this->debug_renewal) echo $thumb_filepath." is the new filepath for new thumb image for ".$new_auction_id."<br>";

							if (!copy (($show_url->FILE_PATH.$show_url->THUMB_FILENAME),$thumb_filepath))
							{
								$thumb_copied = 0;
								if ($this->debug_renewal) echo $thumb_filepath." was NOT copied<br>";
							}
							else
							{
								$thumb_copied = 1;
								if ($this->debug_renewal) echo $thumb_filepath." was copied<br>";
							}

						}

						if (($full_copied) || ($thumb_copied))
						{
							$start_sql_query = "insert into ".$this->images_urls_table."
								(classified_id,image_text,image_width,image_height,original_image_width,original_image_height, date_entered,display_order,filesize,filesize_displayed,icon,file_path";
							$end_sql_query = " values (".$new_auction_id.",\"".$show_url->IMAGE_TEXT."\",".$show_url->IMAGE_WIDTH.",".$show_url->IMAGE_HEIGHT.",".$show_url->ORIGINAL_IMAGE_WIDTH.",
								".$show_url->ORIGINAL_IMAGE_HEIGHT.",".$this->shifted_time($db).",\"".$show_url->DISPLAY_ORDER."\",\"".$show_url->FILESIZE."\",\"".$show_url->FILESIZE_DISPLAYED."\",\"".$show_url->ICON."\",\"".$classified_configuration_data->IMAGE_UPLOAD_PATH."\"";
							if ($full_copied)
							{
								$start_sql_query .= ",full_filename,image_url";

								$end_sql_query .=",\"".$full_filename."\",\"".$classified_configuration_data->URL_IMAGE_DIRECTORY.$full_filename."\"";
							}
							if ($thumb_copied)
							{
								$start_sql_query .= ",thumb_filename,thumb_url";

								$end_sql_query .=",\"".$thumb_filename."\",\"".$classified_configuration_data->URL_IMAGE_DIRECTORY.$thumb_filename."\"";
							}

							$start_sql_query .= ")";
							$end_sql_query .=")";
							$this->sql_query = $start_sql_query.$end_sql_query;
							$insert_url_result = $db->Execute($this->sql_query);
							if ($this->debug_renewal) echo $this->sql_query."<br>";
							if (!$insert_url_result)
							{
								if ($this->debug_renewal) echo $this->sql_query."<br>";
								return false;
							}
						}
					}
				}
			}

			if ($this->debug_renewal)
				echo "setting ".$new_auction_id." as the current auction_id<BR>\n";
			$this->classified_id = $new_auction_id;

			return true;
		}
	} //end of function copy_old_auction_to_new

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function renew_upgrade_by_session ($db)
	{
		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_bolding_feature']." is USE_BOLDING_FEATURE<bR>\n";
			echo $this->renew_upgrade_variables["bolding"]." is bolding chosen<BR>\n";
		}
		if (($this->configuration_data['use_bolding_feature']) &&
			(($this->renew_upgrade_variables["bolding"]) || ($this->renew_upgrade_variables["bolding_upgrade"])))
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				bolding = \"1\"
				where id = ".$this->classified_id;
			$bolding_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$bolding_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[853])."</td>\n</tr>\n";
		}

		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_better_placement_feature']." is USE_BETTER_PLACEMENT_FEATURE<bR>\n";
			echo $this->renew_upgrade_variables["better_placement"]." is better_placement chosen<BR>\n";
		}

		if (($this->configuration_data['use_better_placement_feature']) &&
			(($this->renew_upgrade_variables["better_placement"]) || ($this->renew_upgrade_variables["better_placement_upgrade"])))
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				better_placement = 1
				where id = ".$this->classified_id;
			$better_placement_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$better_placement_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[854])."</td>\n</tr>\n";
		}

		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_featured_feature']." is USE_FEATURED_FEATURE<bR>\n";
			echo $this->renew_upgrade_variables["featured_ad"]." is featured_ad chosen<BR>\n";
		}

		if (($this->configuration_data['use_featured_feature']) &&
			(($this->renew_upgrade_variables["featured_ad"]) || ($this->renew_upgrade_variables["featured_ad_upgrade"])))
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				featured_ad = 1
				where id = ".$this->classified_id;
			$featured_ad_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$featured_ad_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[855])."</td>\n\t</tr>\n";
		}

		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_featured_feature_2']." is USE_FEATURED_FEATURE_2<bR>\n";
			echo $this->renew_upgrade_variables["featured_ad_2"]." is featured_ad_2 chosen<BR>\n";
		}

		if (($this->configuration_data['use_featured_feature_2']) &&
			(($this->renew_upgrade_variables["featured_ad_2"]) || ($this->renew_upgrade_variables["featured_ad_2_upgrade"])))
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				featured_ad_2 = 1
				where id = ".$this->classified_id;
			$featured_ad_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$featured_ad_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[2284])."</td>\n\t</tr>\n";
		}

		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_featured_feature_3']." is USE_FEATURED_FEATURE_3<bR>\n";
			echo $this->renew_upgrade_variables["featured_ad_3"]." is featured_ad_3 chosen<BR>\n";
		}

		if (($this->configuration_data['use_featured_feature_3']) &&
			(($this->renew_upgrade_variables["featured_ad_3"])  || ($this->renew_upgrade_variables["featured_ad_3_upgrade"])))
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				featured_ad_3 = 1
				where id = ".$this->classified_id;
			$featured_ad_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$featured_ad_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[2285])."</td>\n\t</tr>\n";
		}

		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_featured_feature_4']." is USE_FEATURED_FEATURE_4<bR>\n";
			echo $this->renew_upgrade_variables["featured_ad_4"]." is featured_ad_4 chosen<BR>\n";
		}

		if (($this->configuration_data['use_featured_feature_4']) &&
			(($this->renew_upgrade_variables["featured_ad_4"]) || ($this->renew_upgrade_variables["featured_ad_4_upgrade"])))
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				featured_ad_4 = 1
				where id = ".$this->classified_id;
			$featured_ad_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$featured_ad_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[2286])."</td>\n\t</tr>\n";
		}

		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_featured_feature_5']." is USE_FEATURED_FEATURE_5<bR>\n";
			echo $this->renew_upgrade_variables["featured_ad_5"]." is featured_ad_5 chosen<BR>\n";
		}

		if (($this->configuration_data['use_featured_feature_5']) &&
			(($this->renew_upgrade_variables["featured_ad_5"]) || ($this->renew_upgrade_variables["featured_ad_5_upgrade"])))
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				featured_ad_5 = 1
				where id = ".$this->classified_id;
			$featured_ad_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$featured_ad_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[2287])."</td>\n\t</tr>\n";
		}

		if ($this->debug_renewals)
		{
			echo $this->configuration_data['use_attention_getters']." is USE_ATTENTION_GETTERS<bR>\n";
			echo $this->renew_upgrade_variables["attention_getter"]." is attention_getter chosen<BR>\n";
		}

		if (($this->configuration_data['use_attention_getters']) &&
			(($this->renew_upgrade_variables["attention_getter"]) || ($this->renew_upgrade_variables["attention_getter_upgrade"])))
		{
			if (($this->renew_upgrade_variables["attention_getter"]) && ($this->renew_upgrade_variables["attention_getter"] != 0))
				$attention_getter_choice = $this->renew_upgrade_variables["attention_getter_choice"];
			elseif (($this->renew_upgrade_variables["attention_getter_upgrade"]) && ($this->renew_upgrade_variables["attention_getter_upgrade"] != 0))
				$attention_getter_choice = $this->renew_upgrade_variables["attention_getter_choice_upgrade"];
			else
				return true;
			$this->sql_query = "select * from ".$this->choices_table." where choice_id = ".$attention_getter_choice;
			$attention_getter_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$attention_getter_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			elseif ($attention_getter_result->RecordCount() == 1)
			{
				$show_attention_getter = $attention_getter_result->FetchNextObject();
				$attention_getter_url = $show_attention_getter->VALUE;
			}
			else
			{
				$this->renew_upgrade_variables["attention_getter"] = 0;
				$this->renew_upgrade_variables["attention_getter_upgrade"] = 0;
				$attention_getter_url = "";
			}

			$this->sql_query = "update ".$this->classifieds_table." set
				attention_getter = 1,
				attention_getter_url = \"".$attention_getter_url."\"
				where id = ".$this->classified_id;
			$update_attention_getter_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			if (!$update_attention_getter_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			$this->body .="<tr>\n\t<td class=success_failure_message>".urldecode($this->messages[856])."</td>\n</tr>\n";
		}
	} //end of function renew_upgrade_by_session

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function renew_length_by_session($db)
	{
		if ($this->renew_upgrade_variables["renewal_length"] > 0)
		{
			if ($this->classified_data->ENDS > $this->shifted_time($db))
				$new_expire = ($this->classified_data->ENDS + ($this->renew_upgrade_variables["renewal_length"] * 86400));
			else
				$new_expire = ($this->shifted_time($db) + ($this->renew_upgrade_variables["renewal_length"]  * 86400));
			$this->sql_query = "update ".$this->classifieds_table." set
				ends = \"".$new_expire."\",
				date = \"".$this->shifted_time($db)."\",
				bolding = 0,
				better_placement = 0,
				featured_ad = 0,
				featured_ad_2 = 0,
				featured_ad_3 = 0,
				featured_ad_4 = 0,
				featured_ad_5 = 0,
				attention_getter = 0,
				live = 1
				where id = ".$this->classified_id;
			$renew_result = $db->Execute($this->sql_query);
			if ($this->debug_renewals) echo $this->sql_query."<br>\n";
			if (!$renew_result)
			{
				if ($this->debug_renewals) echo $this->sql_query." has an error<br>\n";
				return false;
			}
			if ($this->renew_upgrade_variables["use_credit_for_renewal"])
			{
				//deduct a credit
				$this->remove_a_users_credit($db);
			}
		}

	} //end of function renew_length_by_session

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function check_invoice_cutoff($db)
	{
		 if (($this->configuration_data['invoice_cutoff']) && (!$this->configuration_data['positive_balances_only']))
		 {

			$this->sql_query = "select * from ".$this->payment_types_table." where payment_choice_id = 7";
			if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
			$site_balance_result = $db->Execute($this->sql_query);
			if (!$site_balance_result)
			{
				if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
				return false;
			}
			elseif ($site_balance_result->RecordCount() == 1)
			{
				$show_site_balance = $site_balance_result->FetchNextObject();
				if ($show_site_balance->ACCEPTED)
				{
					//this is an accepted payment choice
					//check for unpaid invoices
					//check to see if unpaid invoices are too old
					//get invoice cutoff date
					$invoice_cutoff_date = ($this->shifted_time($db) - ($this->configuration_data['invoice_cutoff'] * 86400));
					$this->sql_query = "select * from ".$this->invoices_table." where user_id = ".$this->classified_user_id." and
						date_paid = 0 and invoice_date < ".$invoice_cutoff_date;
					if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
					$invoice_cutoff_result = $db->Execute($this->sql_query);
					if (!$invoice_cutoff_result)
					{
						if ($this->debug_renewals) echo $this->sql_query."<bR>\n";
						return false;
					}
					elseif ($invoice_cutoff_result->RecordCount() > 0)
					{
						$this->invoice_cutoff = 1;
						return false;
					}
				}
			}
		 }
	} //end of function check_invoice_cutoff

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Renew_upgrade_sellers_ads
?>
