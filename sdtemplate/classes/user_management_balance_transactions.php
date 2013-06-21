<? //user_management_balance_transactions.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_balance extends Site
{
	var $debug_balance = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_balance ($db,$language_id,$classified_user_id=0, $product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id, $product_configuration);

	} //end of function User_management_balance

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_past_balance_transactions($db,$page=0)
	{
		if (($this->configuration_data['use_account_balance']) && ($this->configuration_data['positive_balances_only']))
		{
			$this->page_id = 184;
			$this->get_text($db);

			$this->sql_query = "select count(*) as total_transactions from ".$this->balance_transactions." where user_id = ".$this->classified_user_id." order by date desc ";
			$balance_count_result = $db->Execute($this->sql_query);
			if ($this->debug_balance) echo $this->sql_query."<br>";

			if (!$balance_count_result)
			{
				if ($this->debug_balance) echo $this->sql_query."<br>";
				return false;
			}
			else
			{
				$show_balance_count = $balance_count_result->FetchNextObject();
				$balance_count = $show_balance_count->TOTAL_TRANSACTIONS;
			}

			$this->sql_query = "select * from ".$this->balance_transactions." where user_id = ".$this->classified_user_id." order by date desc ";
			if ($this->debug_balance) echo $this->sql_query."<br>";

			//get which page of transactions to display
			if ($balance_count > 10)
			{
				if ($page)
				{
					//get this page (20) of balance transactions
					if (is_numeric($page))
					{
						$starting_point = (($page-1) * 10);
						$this->sql_query .= " limit ".$starting_point.", 10";
					}
					else
					{
						//bad data inserted
						return false;
					}
				}
				else
				{
					//get the first page (20) of balance transactions
					$this->sql_query .= " limit 0,10";
				}
			}
			$balance_result = $db->Execute($this->sql_query);
			if ($this->debug_balance) echo $this->sql_query."<br>";
			if (!$balance_result)
			{
				return false;
			}
			elseif ($balance_result->RecordCount() > 0)
			{
				//list balance transaction
				if ($this->debug_balance) echo $balance_result->RecordCount()." is count returned<Br>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top colspan=5 width=100%>\n\t\t".urldecode($this->messages[3214])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[3215])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td colspan=5>\n\t\t".urldecode($this->messages[3216])."\n\t</td>\n</tr>\n";

				//current balance
				$user_data = $this->get_user_data($db,$this->classified_user_id);
				$this->body .="<tr class=current_balance>\n\t\t<td colspan=5>".urldecode($this->messages[3217]);
				$this->body .= $this->configuration_data['precurrency']." ".sprintf("%01.2f",$user_data->ACCOUNT_BALANCE)." ".
						$this->configuration_data['postcurrency']." <a href=";
				//link to add to balance
				if ($this->configuration_data['use_ssl_in_sell_process'])
					$this->body .= trim($this->configuration_data['classifieds_ssl_url']);
				else
					$this->body .= trim($this->configuration_data['classifieds_file_name']);
				$this->body .= "?a=29 class=add_to_balance_link>".urldecode($this->messages[3218])."</a></td></tr>";


				$this->body .="<tr class=table_column_headers>\n\t\t<td>\n\t\t".urldecode($this->messages[3219])."\n\t\t</td>\n\t\t"; //transaction id
				$this->body .="<td>\n\t\t".urldecode($this->messages[3220])."\n\t\t</td>\n\t\t";//transaction date
				$this->body .="<td>\n\t\t".urldecode($this->messages[3221])."\n\t\t</td>\n\t\t";//classified info
				$this->body .="<td>\n\t\t".urldecode($this->messages[3222])."\n\t\t</td>\n\t\t";//transaction details
				$this->body .="<td>\n\t\t".urldecode($this->messages[3223])."\n\t\t</td>\n\t</tr>\n\t";//transaction amount

				$this->row_count = 0;
				while ($show_balance = $balance_result->FetchNextObject())
				{
					if (($this->row_count % 2) == 0)
						$css_tag = "result_set_even_rows";
					else
						$css_tag = "result_set_odd_rows";
					$this->body .= "<tr class=".$css_tag."><td valign=top>".$show_balance->TRANSACTION_ID."</td>";
					$this->body .= "<td valign=top>".date("M d,Y",$show_balance->DATE)."</td>";
					if ($show_balance->AD_ID)
					{
						$classified_data = $this->get_classified_data($db, $show_balance->AD_ID);

						if($this->is_class_auctions())
						{
							if($classified_data->ITEM_TYPE == 1)
								$this->body .= "<td valign=top>".urldecode($classified_data->TITLE)." (".$show_balance->AD_ID.") ".urldecode($this->messages[200103])."</td>";
							elseif($classified_data->ITEM_TYPE == 2)
								$this->body .= "<td valign=top>".urldecode($classified_data->TITLE)." (".$show_balance->AD_ID.") ".urldecode($this->messages[200104])."</td>";
						}
						else
							$this->body .= "<td valign=top>".urldecode($classified_data->TITLE)." (".$show_balance->AD_ID.")</td>";

						$this->body .= "<td valign=top>";
						if ($show_balance->RENEWAL == 1)
							$this->body .= urldecode($this->messages[3224]);
						elseif ($show_balance->RENEWAL == 2)
							$this->body .= urldecode($this->messages[3224]);
						else
							$this->body .= urldecode($this->messages[3225]);
						if ((($show_balance->BOLDING) || ($show_balance->BETTER_PLACEMENT) || ($show_balance->FEATURED_AD) ||
							($show_balance->FEATURED_AD_2) || ($show_balance->FEATURED_AD_3) || ($show_balance->FEATURED_AD_4) ||
							($show_balance->FEATURED_AD_5) || ($show_balance->ATTENTION_GETTER)) && (!$show_balance->FINAL_FEE))
						{
							$this->body .= "<br>".urldecode($this->messages[3226])."<br>";
							if ($show_balance->BOLDING) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3227])."<br>";
							if ($show_balance->BETTER_PLACEMENT) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3228])."<br>";
							if ($show_balance->FEATURED_AD) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3229])."<br>";
							if ($show_balance->FEATURED_AD_2) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3230])."<br>";
							if ($show_balance->FEATURED_AD_3) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3231])."<br>";
							if ($show_balance->FEATURED_AD_4) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3232])."<br>";
							if ($show_balance->FEATURED_AD_5) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3233])."<br>";
							if ($show_balance->ATTENTION_GETTER) $this->body .= "&nbsp;&nbsp;+".urldecode($this->messages[3234])."<br>";
						}

						$this->body .= "</td>";
						$this->body .= "<td valign=top> - ".$this->configuration_data['precurrency']." ".
							sprintf("%01.2f",$show_balance->AMOUNT)." ".$this->configuration_data['postcurrency']."</td>";
					}
					else
					{
						$this->body .= "<td valign=top>".urldecode($this->messages[3261])."</td>";
						$this->body .= "<td valign=top>";
						if ($show_balance->APPROVED)
							$this->body .= urldecode($this->messages[3263]);
						else
							$this->body .= urldecode($this->messages[3262]);
						$this->body.= "</td>";
						$this->body .= "<td valign=top> + ".$this->configuration_data['precurrency']." ".
							sprintf("%01.2f",$show_balance->AMOUNT)." ".$this->configuration_data['postcurrency']."</td>";
					}

					$this->body .= "</tr>";
					$this->row_count++;
				} //end of while
				if ($balance_count > 10)
				{
					//display links to other pages of balance transactions
					$number_of_page_results = ceil($balance_count / 10);
					$this->body .= "<tr>\n\t<td class=page_link colspan=5>\n\t".urldecode($this->messages[3264]);
					if ($number_of_page_results < 10)
					{
						for ($i = 1;$i <= $number_of_page_results;$i++)
						{
							if ($page == $i)
							{
								$this->body .=" <b>".$i."</b> ";
							}
							else
							{
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=18&c=".$i." class=page_link>".$i."</a> ";
							}
						}
					}
					else
					{
						$number_of_sections =  ceil($number_of_page_results/10);
						for ($section = 0;$section < $number_of_sections;$section++)
						{
							if (($page > ($section * 10)) && ($page <= (($section+1) * 10)))
							{
								//display the individual pages within this section
								for ($current_page = (($section * 10) + 1);$current_page <= (($section+1) * 10);$current_page++)
								{
									if ($page <= $number_of_page_results)
										$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=18&c=".$current_page." class=page_link>".$current_page."</a> ";
								}

							}
							else
							{
								//display the link to the section
								$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=18&c=".(($section*10)+1)." class=page_link>".(($section*10)+1)."</a>";
							}
							if (($section+1) < $number_of_sections)
								$this->body .= "<font class=page_link>..</font>";
						}
					}

					$this->body .= "\n\t</td>\n</tr>\n";
				}
				$this->body .= "<tr>\n\t<td class=user_management_home_link colspan=5>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[3236])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
			}
			else
			{
				//there are no balance transactions to list
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top width=100%>\n\t\t".urldecode($this->messages[3214])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t\t<td>\n\t\t".urldecode($this->messages[3215])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[3216])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description>\n\t\t<td>\n\t\t".urldecode($this->messages[3235])."\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td class=user_management_home_link>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=user_management_home_link>".urldecode($this->messages[3236])."</a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
			}
			$this->display_page($db);
			return true;
		}
		else
		{
			//balance transactions are not in use so no need to display any
			return false;
		}
	} //end of function show_past_balance_transactions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end of class User_management_balance

?>