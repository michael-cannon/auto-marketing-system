<? //user_management_home.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_home extends Site
{
	var $error_found;
	var $error;

	var $debug_home = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_home ($db,$language_id=0,$classified_user_id=0,$product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id,$product_configuration);

	} //end of function User_management

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_home_body ($db)
	{
		$this->page_id = 43;
		$this->get_text($db);
		if ($this->debug_home) echo $this->classauctions." is classauctions<bR>\n";
		if ($this->classified_user_id)
		{
			// find template for this page
			$this->sql_query = "select home_template from ".$this->site_configuration_table;
			if ($this->debug_home) echo $this->sql_query."<br>";
			$template_result = $db->Execute($this->sql_query);
			if(!$template_result)
			{
				if ($this->debug_home) echo $this->sql_query."<br>";
				return false;
			}
			else
			{
				$home = $template_result->FetchNextObject();
				if(!$home->HOME_TEMPLATE)
				{
					// There is no template assigned yet so use the original menu
					$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
					$this->body .="<tr class=section_title><td valign=top width=100%>\n\t\t".urldecode($this->messages[620])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .="<tr class=user_management_page_title>\n\t\t<td valign=top>\n\t\t".urldecode($this->messages[418])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .="<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[419])."\n\t</td>\n</tr>\n";
					$this->body .="<tr>\n\t\t<td>\n\t\t";
					$this->user_management_menu($db);
					$this->body .="</td>\n\t</tr>\n\t";
					$this->body .="</table>\n\t";
					$this->display_page($db);
					return true;
				}

				$this->sql_query = "select template_code from ".$this->templates_table." where template_id = ".$home->HOME_TEMPLATE;
				if ($this->debug_home) echo $this->sql_query."<br>";
				$template_result = $db->Execute($this->sql_query);
				if(!$template_result)
				{
					if ($this->debug_home) echo $this->sql_query."<br>";
					return false;
				}
				else
					$template_code = $template_result->FetchNextObject();
			}

			$this->body = stripslashes($template_code->TEMPLATE_CODE);

			$section_title = "<font class=section_title>".urldecode($this->messages[620])."</font>";
			$page_title ="<font class=user_management_page_title>".urldecode($this->messages[418])."</font>";
			$description ="<font class=page_description>".urldecode($this->messages[419])."</font>";
			$active_ads = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1 class=user_links>".urldecode($this->messages[420])."</a>";
			$expired_ads = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2 class=user_links>".urldecode($this->messages[421])."</a>";
			$current_info = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=3 class=user_links>".urldecode($this->messages[422])."</a>";
			$place_ad = "<a href=";
			if ($this->configuration_data['use_ssl_in_sell_process'])
				$place_ad .= trim($this->configuration_data['classifieds_ssl_url']);
			else
				$place_ad .= trim($this->configuration_data['classifieds_file_name']);
			$place_ad .= "?a=1 class=user_links>".urldecode($this->messages[423])."</a>";
			$ad_filters = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=9 class=user_links>".urldecode($this->messages[424])."</a>";
			$favorites = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=10 class=user_links>".urldecode($this->messages[427])."</a>";
			$comm = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=8 class=user_links>".urldecode($this->messages[425])."</a>";
			$comm_config = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=7 class=user_links>".urldecode($this->messages[426])."</a>";
			$signs_flyers = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=12 class=user_links>".urldecode($this->messages[1182])."</a>";

			$this->body = str_replace("<<SECTION_TITLE>>", $section_title, $this->body);
			$this->body = str_replace("<<PAGE_TITLE>>", $page_title, $this->body);
			$this->body = str_replace("<<DESCRIPTION>>", $description, $this->body);
			$this->body = str_replace("<<ACTIVE_ADS>>", $active_ads, $this->body);
			$this->body = str_replace("<<EXPIRED_ADS>>", $expired_ads, $this->body);
			$this->body = str_replace("<<CURRENT_INFO>>", $current_info, $this->body);
			$this->body = str_replace("<<PLACE_AD>>", $place_ad, $this->body);
			$this->body = str_replace("<<AD_FILTERS>>", $ad_filters, $this->body);
			$this->body = str_replace("<<FAVORITES>>", $favorites, $this->body);
			$this->body = str_replace("<<COMMUNICATIONS>>", $comm, $this->body);
			$this->body = str_replace("<<COMMUNICATIONS_CONFIG>>", $comm_config, $this->body);
			$this->body = str_replace("<<SIGNS_AND_FLYERS>>", $signs_flyers, $this->body);

			if($this->is_class_auctions() || $this->is_auctions())
			{
				// TODO these below needs to be changed
				$feedback = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=22 class=user_links>".urldecode($this->messages[200017])."</a>";
				$current_bids = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=21 class=user_links>".urldecode($this->messages[200018])."</a>";
				
				if ($this->configuration_data['black_list_of_buyers'])
					$blacklist_buyers ="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=19 class=user_links>".urldecode($this->messages[200019])."</a>";
				else 	
					$blacklist_buyers = "";
				if ($this->configuration_data['invited_list_of_buyers'])
					$invited_buyers ="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=20 class=user_links>".urldecode($this->messages[200020])."</a>";
				else 
					$invited_buyers = "";

				// Auction specific ones
				$this->body = str_replace("<<FEEDBACK>>", $feedback, $this->body);
				$this->body = str_replace("<<CURRENT_BIDS>>", $current_bids, $this->body);
				$this->body = str_replace("<<BLACKLIST_BUYERS>>", $blacklist_buyers, $this->body);
				$this->body = str_replace("<<INVITED_BUYERS>>", $invited_buyers, $this->body);
			}
			else
			{
				$this->body = str_replace("<<FEEDBACK>>", "", $this->body);
				$this->body = str_replace("<<CURRENT_BIDS>>", "", $this->body);
				$this->body = str_replace("<<BLACKLIST_BUYERS>>", "", $this->body);
				$this->body = str_replace("<<INVITED_BUYERS>>", "", $this->body);
			}

			if ($this->configuration_data['use_account_balance'])
			{
				$user_data = $this->get_user_data($db,$this->classified_user_id);
				// Add money to account and not show balance
				if ($this->configuration_data['positive_balances_only'])
				{
					if ($this->debug_home)
					{
						echo "replacing the BALANCE_TRANSACTIONS link<bR>\n";
						echo urldecode($this->messages[3213])." is the text for the link<bR>\n";
					}
					$balance = "<span class=user_links>".urldecode($this->messages[2549]).
						$this->configuration_data['precurrency']." ".sprintf("%01.2f",$user_data->ACCOUNT_BALANCE)." "
						.$this->configuration_data['postcurrency']."</span>";

					$add_to_account = "<a href=";
					if ($this->configuration_data['use_ssl_in_sell_process'])
						$add_to_account .= trim($this->configuration_data['classifieds_ssl_url']);
					else
						$add_to_account .= trim($this->configuration_data['classifieds_file_name']);
					$add_to_account .= "?a=29 class=user_links>".urldecode($this->messages[2548])."</a>";

					$this->body = str_replace("<<ADD_MONEY_WITH_BALANCE>>", $balance, $this->body);
					$this->body = str_replace("<<ADD_MONEY>>", $add_to_account, $this->body);

					$balance_link = "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=18 class=user_links>".urldecode($this->messages[3213])."</a>";
					$this->body = str_replace("<<BALANCE_TRANSACTIONS>>", $balance_link, $this->body);

					$this->body = str_replace("<<UNPAID_INVOICES>>", "", $this->body);
					$this->body = str_replace("<<PAID_INVOICES>>", "", $this->body);
				}
				else
				{
					//show links to unpaid invoices and paid invoices
					$unpaid_invoices = "<a href=".trim($this->configuration_data['classifieds_file_name']);
					$unpaid_invoices .="?a=4&b=16 class=user_links>".urldecode($this->messages[3171])."</a>";


					$to_be_invoiced = 0;
					$this->sql_query = "select * from ".$this->balance_transactions." where (auction_id != 0 or ad_id != 0 or subscription_renewal != 0) and cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$this->classified_user_id;
					if ($this->debug_info) echo $this->sql_query."<br>";
					$invoice_total_result = $db->Execute($this->sql_query);
					if (!$invoice_total_result)
					{
						if ($this->debug_info) echo $this->sql_query."<br>";
						return false;
					}
					elseif ($invoice_total_result->RecordCount() > 0)
					{
						while ($show_invoices = $invoice_total_result->FetchNextObject())
						{
							$to_be_invoiced = $to_be_invoiced + $show_invoices->AMOUNT;
						}
					}
					if ($to_be_invoiced > 0)
					{
						$balance = "<span class=user_links>".urldecode($this->messages[2550]);
						$balance .= $this->configuration_data['precurrency']." ".sprintf("%01.2f",$to_be_invoiced)." "
							.$this->configuration_data['postcurrency']."</span>";
					}
					else
					{
						$balance = "";
					}

					$paid_invoices .="<a href=".trim($this->configuration_data['classifieds_file_name']);
					$paid_invoices .="?a=4&b=15 class=user_links>".urldecode($this->messages[3172])."</a>";

					$this->body = str_replace("<<UNPAID_INVOICES>>", $unpaid_invoices, $this->body);
					$this->body = str_replace("<<PAID_INVOICES>>", $paid_invoices, $this->body);

					$this->body = str_replace("<<BALANCE_TRANSACTIONS>>", "", $this->body);
					$this->body = str_replace("<<ADD_MONEY_WITH_BALANCE>>", $balance, $this->body);
					$this->body = str_replace("<<ADD_MONEY>>", "", $this->body);
				}
			}
			else
			{
				$this->body = str_replace("<<UNPAID_INVOICES>>", "", $this->body);
				$this->body = str_replace("<<PAID_INVOICES>>", "", $this->body);

				$this->body = str_replace("<<BALANCE_TRANSACTIONS>>", "", $this->body);
				$this->body = str_replace("<<ADD_MONEY_WITH_BALANCE>>", "", $this->body);
				$this->body = str_replace("<<ADD_MONEY>>", "", $this->body);
			}

			$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			$user_group_result = $db->Execute($this->sql_query);
			if ($this->debug_home) echo $this->sql_query."<br>";
			if (!$user_group_result)
			{
				if ($this->debug_home) echo $this->sql_query."<br>";
				return false;
			}
			elseif ($user_group_result->RecordCount() == 1)
			{
				$show_user_stuff = $user_group_result->FetchNextObject();
				$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$show_user_stuff->PRICE_PLAN_ID;
				$price_plan_result = $db->Execute($this->sql_query);
				if ($this->debug_home) echo $this->sql_query."<br>";
				if (!$price_plan_result)
				{
					if ($this->debug_home) echo $this->sql_query."<br>";
					return false;
				}
				elseif ($price_plan_result->RecordCount() == 1)
				{
					$base_price_plan = $price_plan_result->FetchNextObject();
					if ($base_price_plan->TYPE_OF_BILLING == 2)
					{
						$subscription = "<a href=".$this->configuration_data['classifieds_file_name']."?a=24 class=user_links>".urldecode($this->messages[1695])."</a>";
						$subscription = "<a href=";
						if ($this->configuration_data['use_ssl_in_sell_process'])
							$subscription .= trim($this->configuration_data["classifieds_ssl_url"]);
						else
							$subscription .= trim($this->configuration_data["classifieds_file_name"]);						
						$subscription .= "?a=24 class=user_links>".urldecode($this->messages[1695])."</a>";
						$this->body = str_replace("<<RENEW_EXTEND_SUBSCRIPTION>>", $subscription, $this->body);
					}
					else
					{
						$subscription = "";
						$this->body = str_replace("<<RENEW_EXTEND_SUBSCRIPTION>>", $subscription, $this->body);
					}
				}
			}
			else
			{
				$subscription = "";
				$this->body = str_replace("<<RENEW_EXTEND_SUBSCRIPTION>>", $subscription, $this->body);
			}

			// Display the page
			$this->display_page($db);
			return true;
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function user_management_home_body

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_menu ($db,$switch=0)
	{
		if ($this->classified_user_id)
		{
			$this->body .="<table cellpadding=0 cellspacing=1 border=0 align=center width=100% class=user_management_menu_links>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=1 class=user_links>".urldecode($this->messages[420])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=2 class=user_links>".urldecode($this->messages[421])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=3 class=user_links>".urldecode($this->messages[422])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=";
			if ($this->configuration_data['use_ssl_in_sell_process'])
				$this->body .=trim($this->configuration_data['classifieds_ssl_url']);
			else
				$this->body .= trim($this->configuration_data['classifieds_file_name']);
			$this->body .= "?a=1 class=user_links>".urldecode($this->messages[423])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=9 class=user_links>".urldecode($this->messages[424])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=10 class=user_links>".urldecode($this->messages[427])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=8 class=user_links>".urldecode($this->messages[425])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=7 class=user_links>".urldecode($this->messages[426])."</a>\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=12 class=user_links>".urldecode($this->messages[1182])."</a>\n\t\t</td>\n\t</tr>\n\t";

			$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
			$user_group_result = $db->Execute($this->sql_query);
			if ($this->debug_home) echo $this->sql_query."<br>";
			if (!$user_group_result)
			{
				if ($this->debug_home) echo $this->sql_query."<br>";
				return false;
			}
			elseif ($user_group_result->RecordCount() == 1)
			{
				$show_user_stuff = $user_group_result->FetchNextObject();
				$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$show_user_stuff->PRICE_PLAN_ID;
				$price_plan_result = $db->Execute($this->sql_query);
				if ($this->debug_home) echo $this->sql_query."<br>";
				if (!$price_plan_result)
				{
					if ($this->debug_home) echo $this->sql_query."<br>";
					return false;
				}
				elseif ($price_plan_result->RecordCount() == 1)
				{
					$base_price_plan = $price_plan_result->FetchNextObject();
					if ($base_price_plan->TYPE_OF_BILLING == 2)
					{
						$this->body .= "<tr>\n\t<td  class=user_links>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=24 class=user_links>".urldecode($this->messages[1695])."</td>\n</tr>\n";
					}
				}
			}
			if ($this->configuration_data['use_account_balance'])
			{
				$user_data = $this->get_user_data($db,$this->classified_user_id);

				// Show balances
				if ($this->configuration_data['positive_balances_only'])
				{
					$this->body .="<tr class=user_links>\n\t\t<td><a href=";
					if ($this->configuration_data['use_ssl_in_sell_process'])
						$this->body .= trim($this->configuration_data['classifieds_ssl_url']);
					else
						$this->body .= trim($this->configuration_data['classifieds_file_name']);
					$this->body .= "?a=29 class=user_links>".urldecode($this->messages[2548])."</a> ".urldecode($this->messages[2549])." ".
						$this->configuration_data['precurrency']." ".sprintf("%01.2f",$user_data->ACCOUNT_BALANCE)." ".
						$this->configuration_data['postcurrency']."</td>\n</tr>\n";

					$this->body .="<tr class=user_links>\n\t\t<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=18 class=user_links>".urldecode($this->messages[103304])."</a></td></tr>";

					//$this->body .="<tr class=user_links>\n\t\t<td><a href=";
					//if ($this->configuration_data['use_ssl_in_sell_process'])
					//	$this->body .= trim($this->configuration_data['auctions_ssl_url']);
					//else
					//	$this->body .= trim($this->configuration_data['auctions_file_name']);
					//$this->body .= "?a=1033 class=user_links>".urldecode($this->messages[103122])."</a></td>\n</tr>\n";
				}
				else
				{
					//show links to unpaid invoices and paid invoices
					$this->body .="<tr class=user_links>\n\t\t<td><a href=";
					if ($this->configuration_data['use_ssl_in_sell_process'])
						$this->body .=trim($this->configuration_data['classifieds_ssl_url']);
					else
						$this->body .= trim($this->configuration_data['classifieds_file_name']);
					$this->body .="?a=4&b=15 class=user_links>".urldecode($this->messages[3171])."</a></td>\n</tr>\n";

					$this->body .="<tr class=user_links>\n\t\t<td><a href=";
					if ($this->configuration_data['use_ssl_in_sell_process'])
						$this->body .=trim($this->configuration_data['classifieds_ssl_url']);
					else
						$this->body .= trim($this->configuration_data['classifieds_file_name']);
					$this->body .="?a=4&b=16 class=user_links>".urldecode($this->messages[3172])."</a></td>\n</tr>\n";
				}
			}

			$this->body .="</table>\n\t";
		}
		else
		{
			//no user id
			$this->error_message = $this->data_missing_error_message;
			return false;
		}
	} //end of function user_management_menu

} //end of class User_management_class

?>