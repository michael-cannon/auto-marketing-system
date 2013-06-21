<? //user_management_invoices.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_invoices extends Site
{
	var $debug_invoices = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_invoices ($db,$language_id,$classified_user_id=0, $product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id);
	} //end of function User_management_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_unpaid_invoices($db)
	{
		if (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']))
		{
			$this->page_id = 182;
			$this->get_text($db);
			$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
			$this->body .="<tr class=user_management_page_title><td valign=top colspan=5 width=100%>\n\t\t".urldecode($this->messages[3148])."\n\t\t</td>\n\t</tr>\n\t";
			$this->body .="<tr class=page_title>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[3149])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[3150])."\n\t</td>\n</tr>\n";
			$this->body .="<tr class=page_description>\n\t\t<td colspan=5>".urldecode($this->messages[3242]);
			//get current accumulated but unbilled charges
			$this->sql_query = "select * from ".$this->balance_transactions." where (auction_id != 0 or ad_id != 0) and cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$this->classified_user_id;
			if ($this->debug_sell) echo $this->sql_query."<br>";
			$invoice_total_result = $db->Execute($this->sql_query);
			if (!$invoice_total_result)
			{
				return false;
			}
			elseif ($invoice_total_result->RecordCount() > 0)
			{
				$to_be_invoiced = 0;
				while ($show_invoices = $invoice_total_result->FetchNextObject())
				{
					$to_be_invoiced = $to_be_invoiced + $show_invoices->AMOUNT;
				}
				$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$to_be_invoiced)." ".$this->configuration_data['postcurrency']."\n\t\t</td></tr>\n\t";
			}
			else
			{
				$this->body .= " ".$this->configuration_data['precurrency']." ".sprintf("%01.2f",0)." ".$this->configuration_data['postcurrency']."\n\t\t</td></tr>\n\t";
			}

			//show current unpaid invoices
			$this->sql_query = "select * from ".$this->invoices_table." where date_paid = 0 and user_id = ".$this->classified_user_id." order by invoice_date asc";
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$invoice_result = $db->Execute($this->sql_query);
			if (!$invoice_result)
			{
				return false;
			}
			elseif ($invoice_result->RecordCount() > 0)
			{
				//list open invoices
				if ($this->debug_invoices) echo $invoice_result->RecordCount()." is count returned<Br>\n";
				$this->body .="<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[3151])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[3152])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[3153])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t&nbsp;</a>\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t&nbsp;</a>\n\t\t</td>\n\t</tr>\n\t";

				$this->row_count = 0;
				while ($show_invoice = $invoice_result->FetchNextObject())
				{
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					$this->body .= "<tr class=".$css_tag."><td>".$show_invoice->INVOICE_ID."</td>";
					$this->body .= "<td>".date("M d,Y",$show_invoice->INVOICE_DATE)."</td>";
					$this->body .= "<td>".$this->configuration_data['precurrency']." ".
						sprintf("%01.2f",$this->get_invoice_total($db,$show_invoice->INVOICE_ID))." ".$this->configuration_data['postcurrency']."</td>";
					$this->body .= "<td><a href=";
					if ($this->configuration_data['use_ssl_in_sell_process'])
						$this->body .= trim($this->configuration_data['classifieds_ssl_url']);
					else
						$this->body .= trim($this->configuration_data['classifieds_file_name']);	
					$this->body .="?a=30&b=".$show_invoice->INVOICE_ID.">".urldecode($this->messages[3154])."</a></td>";
					$this->body .= "<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=17&c=".$show_invoice->INVOICE_ID.">".urldecode($this->messages[3155])."</a></td></tr>";
					$this->row_count++;
				} //end of while
				$this->body .= "<tr>\n\t<td class=user_management_home_link colspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[3165])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
			}
			else
			{
				$this->body .="<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[3156])."\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td class=user_management_home_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[3165])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}
	} //end of function show_unpaid_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_paid_invoices($db)
	{
		if (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']))
		{
			$this->page_id = 181;
			$this->get_text($db);
			//show current unpaid invoices
			$this->sql_query = "select * from ".$this->invoices_table." where date_paid != 0 and user_id = ".$this->classified_user_id." order by invoice_date asc";
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$invoice_result = $db->Execute($this->sql_query);
			if (!$invoice_result)
			{
				return false;
			}
			elseif ($invoice_result->RecordCount() > 0)
			{
				if ($this->debug_invoices) echo $invoice_result->RecordCount()." is the invoices returned<br>\n";
				//list open invoices
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top colspan=4 width=100%>\n\t\t".urldecode($this->messages[3157])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[3158])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[3159])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[3160])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[3161])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t".urldecode($this->messages[3162])."\n\t\t</td>\n\t\t";
				$this->body .="<td>\n\t\t&nbsp;</a>\n\t\t</td>\n\t</tr>\n\t";

				$this->row_count = 0;
				while ($show_invoice = $invoice_result->FetchNextObject())
				{
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					$this->body .= "<tr class=".$css_tag."><td>".$show_invoice->INVOICE_ID."</td>";
					$this->body .= "<td>".date("M d,Y",$show_invoice->INVOICE_DATE)."</td>";
					$this->body .= "<td>".$this->configuration_data['precurrency']." ".
						sprintf("%01.2f",$this->get_invoice_total($db,$show_invoice->INVOICE_ID)).$this->configuration_data['postcurrency']."</td>";
					$this->body .= "<td><a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=17&c=".$show_invoice->INVOICE_ID.">".urldecode($this->messages[3163])."</a></td></tr>";
					$this->row_count++;
				} //end of while
				$this->body .= "<tr class=user_management_home_link>\n\t<td colspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[3166])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
			}
			else
			{
				if ($this->debug_invoices) echo "no invoices returned<br>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top width=100%>\n\t\t".urldecode($this->messages[3157])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t\t<td>\n\t\t".urldecode($this->messages[3158])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[3159])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[3164])."\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td class=user_management_home_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[3166])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			return false;
		}
	} //end of function show_unpaid_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_invoice_total($db,$invoice_id=0)
	{
		if (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']))
		{
			if ($invoice_id)
			{
				$this->sql_query = "select * from ".$this->balance_transactions." where invoice_id = ".$invoice_id;
				if ($this->debug_invoices) echo $this->sql_query."<br>";
				$invoice_item_result = $db->Execute($this->sql_query);
				if (!$invoice_item_result)
				{
					return false;
				}
				else
				{
					$total = 0;
					while ($show_item = $invoice_item_result->FetchNextObject())
					{
						$total = $total + $show_item->AMOUNT;
					} // end of while
					return $total;
				}
			}
			else
				return false;
		}
		else
		{
			return false;
		}
	} // end of function get_invoice_total

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_invoice ($db,$invoice_id=0)
	{
		if (($this->configuration_data['use_account_balance']) && (!$this->configuration_data['positive_balances_only']))
		{
			//show an individual invoice
			$this->page_id = 183;
			$this->get_text($db);
			if ($invoice_id)
			{
				$this->sql_query = "select * from  ".$this->invoices_table." where invoice_id = ".$invoice_id." and user_id = ".$this->classified_user_id;
				if ($this->debug_invoices) echo $this->sql_query."<br>";
				$invoice_result = $db->Execute($this->sql_query);
				if (!$invoice_result)
				{
					return false;
				}
				elseif ($invoice_result->RecordCount() == 1)
				{
					$show_invoice = $invoice_result->FetchNextObject();
					$user_data = $this->get_user_data($db,$show_invoice->USER_ID);
					//show the specifics of this invoice
					$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
					$this->body .="<tr class=user_management_page_title><td valign=top colspan=4 width=100%>\n\t\t".urldecode($this->messages[3173])."\n\t\t</td>\n\t</tr>\n\t";
					$this->body .="<tr class=page_title>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[3174])."\n\t</td>\n</tr>\n";
					$this->body .="<tr class=page_description>\n\t\t<td colspan=4>\n\t\t".urldecode($this->messages[3175])."\n\t</td>\n</tr>\n";
					$this->body .= "<tr><td class=cost_field_labels>".urldecode($this->messages[3176])."</td>";
					$this->body .= "<td class=cost_data_values>".$show_invoice->INVOICE_ID."</td></tr>";
					$this->body .= "<tr><td class=cost_field_labels>".urldecode($this->messages[3203])."</td>";
					$this->body .= "<td class=cost_data_values>".date("M d,Y",$show_invoice->INVOICE_DATE)."</td></tr>";
					if ($show_invoice->DATE_PAID)
					{
						$this->body .= "<tr><td class=cost_field_labels>".urldecode($this->messages[3202])."</td>";
						$this->body .= "<td class=cost_data_values>".date("M d,Y",$show_invoice->DATE_PAID)."</td></tr>";
					}
					$this->body .= "<tr><td class=cost_field_labels>".urldecode($this->messages[3177])."</td>";
					$this->body .= "<td class=cost_data_values>".$this->configuration_data['precurrency']." ".$this->get_invoice_total($db,$show_invoice->INVOICE_ID)." ".$this->configuration_data['postcurrency']."</td></tr>";

					$this->sql_query = "select * from  ".$this->balance_transactions."  where invoice_id = ".$invoice_id;
					if ($this->debug_invoices) echo $this->sql_query."<br>";
					$invoice_items_result = $db->Execute($this->sql_query);
					if (!$invoice_items_result)
					{
						return false;
					}
					elseif ($invoice_items_result->RecordCount() > 0)
					{
						$this->row_count = 0;
						$this->body .= "<tr><td colspan=2><table width=100% cellpadding=2 cellspacing=1 border=0>";
						$this->body .= "<tr class=table_column_headers><td colspan=5>".urldecode($this->messages[3178])."</td></tr>";
						$this->body .="<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[3179])."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".urldecode($this->messages[3181])."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".urldecode($this->messages[3190])."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".urldecode($this->messages[3180])."\n\t\t</td>\n\t\t";
						$this->body .="<td>\n\t\t".urldecode($this->messages[3182])."\n\t\t</td>\n\t\t";
						$this->body .="</tr>\n\t";
						while ($show_item = $invoice_items_result->FetchNextObject())
						{
							if (($this->row_count % 2) == 0)
								$css_tag = "result_set_even_rows";
							else
								$css_tag = "result_set_odd_rows";
							$classified_data = $this->get_classified_data($db,$show_item->AD_ID);
							$this->body .= "<tr class=".$css_tag."><td>".$show_item->AD_ID."</td>";
							$this->body .= "<td>".urldecode($classified_data->TITLE)."</td>";

							$this->body .= "<td valign=top>".$this->small_font_tag;
							if ($show_item->RENEWAL)
								$this->body .= urldecode($this->messages[3192]);
							else
								$this->body .= urldecode($this->messages[3191]);
							if (($show_item->BOLDING) || ($show_item->BETTER_PLACEMENT) || ($show_item->FEATURED_AD) ||
								($show_item->FEATURED_AD_2) || ($show_item->FEATURED_AD_3) || ($show_item->FEATURED_AD_4) ||
								($show_item->FEATURED_AD_5) || ($show_item->ATTENTION_GETTER))
							{
								$this->body .= "<br>".urldecode($this->messages[3201])."<br>";
								if ($show_item->BOLDING) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3193])."<br>";
								if ($show_item->BETTER_PLACEMENT) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3194])."<br>";
								if ($show_item->FEATURED_AD) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3195])."<br>";
								if ($show_item->FEATURED_AD_2) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3196])."<br>";
								if ($show_item->FEATURED_AD_3) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3197])."<br>";
								if ($show_item->FEATURED_AD_4) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3198])."<br>";
								if ($show_item->FEATURED_AD_5) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3199])."<br>";
								if ($show_item->ATTENTION_GETTER) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3200])."<br>";
							}
							$this->body .= "</td>";
							$this->body .= "<td>".date("M d,Y",$show_item->DATE)."</td>";
							$this->body .= "<td>".$this->configuration_data['precurrency']." ".$show_item->AMOUNT." ".$this->configuration_data['postcurrency']."</td></tr>";
						}
						$this->body .= "</table></td></tr>";
					}
					$this->body .= "<tr>\n\t<td colspan=2 class=back_to_my_account_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=15 class=back_to_my_account_link>".urldecode($this->messages[3183])."</a>\n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td colspan=2 class=back_to_my_account_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=16 class=back_to_my_account_link>".urldecode($this->messages[3184])."</a>\n\t</td>\n</tr>\n";
					$this->body .= "<tr>\n\t<td colspan=2 class=back_to_my_account_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=back_to_my_account_link>".urldecode($this->messages[3185])."</a>\n\t</td>\n</tr>\n";
					$this->body .= "</table>";
					$this->display_page($db);
					return true;
				}
				else
					return false;
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

	} //end of function show_invoice

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class User_management_invoices

?>