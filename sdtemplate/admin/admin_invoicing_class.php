<? //admin_invoicing_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Admin_invoicing extends Admin_site {

	var $debug_invoices = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Admin_invoicing($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function invoicing_home($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"invoice cutoff\", \"This is the number of days after an invoice is created for a user that their account will remain \\\"live\\\" before listing placement/renewal/upgrade privileges are revoked.\"]\n";

		//".$this->show_tooltip(1,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";
		$this->get_configuration_data($db);
		//check whether using a site balance to pay for listings
		$this->sql_query = "select * from ".$this->classified_payment_choices_table." where payment_choice_id = 7";
		if ($this->debug_invoices) echo $this->sql_query."<br>";
		$site_balance_result = $db->Execute($this->sql_query);
		if (!$site_balance_result)
		{
			return false;
		}
		elseif ($site_balance_result->RecordCount() == 1)
		{
			$show_site_balance = $site_balance_result->FetchRow();

			if (($show_site_balance["accepted"]) && (!$this->configuration_data["positive_balances_only"]))
			{
				$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";

				//site balance is accepted as a form of payment
				//and negative balances are allowed

				//link to set invoices for all site
				//echo "<tr><td colspan=2 class=medium_font><b>Transactions > Create Invoices </b></td></tr>";
				$this->title .= "Transactions > Create Invoices";
				$this->body .= "<tr class=row_color2>\n\t<td width=50% class=medium_font align=center>\n\t<a href=index.php?a=78&z=3><span class=medium_font><b>click to create invoices for all users</b></span></a>\n\t</td>\n";
				//link to set invoices for individual
				$this->body .= "<td width=50% class=medium_font>\n\tTo create an invoice for a specific user find that users information and click the create invoice link within their account information page<Br>
					<a href=index.php?a=19><span class=medium_font>list users</span></a>
					<br><a href=index.php?a=16><span class=medium_font>search for a user</span></a>\n\t</td>\n</tr>\n";

				//link to set a cutoff for account
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light>\n\t<b>Invoice Configuration</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr><td width=50% align=right class=medium_font><b>invoice cutoff:</b>".$this->show_tooltip(1,1)."</td>";
				if ($this->configuration_data["invoice_cutoff"])
				{
					$this->body .= "<td class=medium_font>".$this->configuration_data["invoice_cutoff"]." days
						 <a href=index.php?a=78&z=5><span class=medium_font>click here to change</span></a></td></tr>";
				}
				else
				{
					$this->body .= "<td colspan=2 class=medium_font>There currently is no account cuttoff
						<a href=index.php?a=78&z=5><span class=medium_font>click here to change</span></a></td></tr>";
				}

				// Set max invoice
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=78&z=9 method=post>";
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light>\n\t<b>Set Maximum Invoice Limits</b></font>\n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color_red><td class=medium_font_light><b>Price Plan</b></td><td class=medium_font_light><b>Max Invoice Limit</b></td></tr>";
				$this->sql_query = "select name, invoice_max, price_plan_id from ".$this->price_plan_table;
				$result = $db->Execute($this->sql_query);
				if(!$result)
				{
					//echo $this->sql_query.'<Br>';
					return false;
				}
				$price_plan = $result->GetArray();
				$this->row_count = 0;
				foreach($price_plan as $index => $value)
				{
					$this->body .= "<tr class=".$this->get_row_color()."><td class=medium_font><a href=index.php?a=37&b=3&g=".$value['price_plan_id'].">".$value['name']."</a></td><td class=medium_font>".$this->charge_select_box($value['invoice_max'], "b[".$value['price_plan_id']."]")."</td></tr>";
					$this->row_count++;
				}
				if (!$this->admin_demo()) $this->body .= "<tr><td align=center colspan=2><input type=submit value=\"Save\"></td></tr>";
				$this->body .= "</form>";

				//find invoice id
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=78&z=4 method=post>";
				$this->body .= "<tr bgcolor=000066>\n\t<td colspan=2 class=medium_font_light>\n\t<b>Invoice Search</b> \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color2><td class=medium_font align=right width=55%><b>Enter an Invoice id: </b><input type=text name=b></td>";
				if (!$this->admin_demo()) $this->body .= "<td class=medium_font><input type=submit value=\"Submit\"></td>";
				$this->body .= "</tr>";

				$this->body .= "<tr align=center><td colspan=2><a href=index.php?a=78&z=7><span class=medium_font><b>Display Current Unpaid Invoices</b></span></a></td></tr>";
				$this->body .= "<tr align=center><td colspan=2><a href=index.php?a=78&z=6><span class=medium_font><b>Display Current Paid Invoices</b></span></a></td></tr>";

				$this->body .= "</table>";
				$this->body .= "</form>";

			}
			else
			{
				$this->title .= "Transactions > Invoices Home > Error";
				$this->body .= "<span class=medium_font><b>Your site must accept site balances as a form of payment <Br>and allow negative balances to use the invoicing system</b><Br><Br>";
				$this->body .= "If you want and have not done so, turn on site balance as a form of payment <a href=index.php?a=39&b=2>here</a><Br><Br>";
				$this->body .= "To set the site balance to allow negative balances <a href=index.php?a=39&b=10> click here</a><Br></span><Br>";
			}
			return true;
		}
		else
		{
			//upgrade required
			return false;
		}
		//show unpaid invoices
	} //end of function invoicing_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function unpaid_invoices ($db)
	{
		//show current unpaid invoices
		$this->get_configuration_data($db);
		$this->sql_query = "select * from ".$this->invoices_table." where date_paid = 0 order by invoice_date asc";
		if ($this->debug_invoices) echo $this->sql_query."<br>";
		$invoice_result = $db->Execute($this->sql_query);
		if (!$invoice_result)
		{
			return false;
		}
		elseif ($invoice_result->RecordCount() > 0)
		{
			//list open invoices
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
			$this->title = "Unpaid Invoices";
			$this->body .= "<tr><td><table width=100% cellpadding=2 cellspacing=1 border=0>";
			$this->body .= "<tr  class=row_color_black><td class=medium_font_light>user</td>";
			$this->body .= "<td class=medium_font_light>invoice date</td>";
			$this->body .= "<td class=medium_font_light>amount</td>";
			$this->body .= "<td class=medium_font_light>mark paid</td>";
			$this->body .= "<td class=medium_font_light>&nbsp;</td></tr>";
			$this->row_count = 1;
			while ($show_invoice = $invoice_result->FetchRow())
			{
				$user_data = $this->get_user_data($db,$show_invoice["user_id"]);
				$this->body .= "<tr class=".$this->get_row_color()."><td class=medium_font>".$user_data["username"]." (".$user_data["firstname"]." ".$user_data["lastname"].")</td>";
				$this->body .= "<td class=medium_font>".date("M d,Y G:i - D",$show_invoice["invoice_date"])."</td>";
				//get invoice total

				$this->sql_query = "select sum(amount) as total_of_invoice from ".$this->balance_transactions." where invoice_id = ".$show_invoice["invoice_id"];
				if ($this->debug_invoices) echo $this->sql_query."<br>";
				$total_result = $db->Execute($this->sql_query);
				if (!$total_result)
				{
					echo $this->sql_query."<br>";
					echo "error getting earliest transaction";
					return false;
				}
				elseif ($total_result->RecordCount() == 1)
				{
					$show_total = $total_result->FetchRow();
					$invoice_total = $show_total["total_of_invoice"];
				}
				else
				{
					echo $this->sql_query."<br>";
					echo "error getting number of  transaction";
				}

				$this->body .= "<td><div nowrap><span class=medium_font>".$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$invoice_total)." ".$this->configuration_data["postcurrency"]."</span></div></td>";
				$this->body .= "<td><a href=index.php?a=78&z=2&b=".$show_invoice["invoice_id"]."&c=".$show_invoice["user_id"]."><span class=medium_font>mark paid</span></a></td>";
				$this->body .= "<td><a href=index.php?a=78&z=8&b=".$show_invoice["invoice_id"]."><span class=medium_font>details</span></a></td></tr>";
				$this->row_count++;
			} //end of while
			$this->body .= "</table></td></tr>";
			$this->body .= "<tr class=row_color_black>\n\t<td align=center >\n\t<a href=index.php?a=78>invoice home</a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>";
			return true;
		}
		else
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
			$this->title = "Transactions > Unpaid Invoices";

			$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t<b>There are no unpaid invoices.</b><br><br><bR>";
			$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t<a href=index.php?a=78><b>invoice home</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>";
			return true;
		}

	} //end of function unpaid_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function paid_invoices ($db)
	{
		//show current unpaid invoices
		$this->get_configuration_data($db);
		$this->sql_query = "select * from ".$this->invoices_table." where date_paid != 0 order by date_paid desc";
		if ($this->debug_invoices) echo $this->sql_query."<br>";
		$invoice_result = $db->Execute($this->sql_query);
		if (!$invoice_result)
		{
			return false;
		}
		elseif ($invoice_result->RecordCount() > 0)
		{
			//list open invoices
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
			$this->title = "Paid Invoices";
			$this->body .= "<tr><td><table width=100% cellpadding=2 cellspacing=1 border=0>";
			$this->body .= "<tr  class=row_color_black><td class=medium_font_light>user</td>";
			$this->body .= "<td class=medium_font_light>invoice date</td>";
			$this->body .= "<td class=medium_font_light>date paid</td>";
			$this->body .= "<td class=medium_font_light>amount</td>";
			//echo "<td class=medium_font_light>comments</td>";
			$this->body .= "<td class=medium_font_light>mark unpaid</td>";
			$this->body .= "<td class=medium_font_light>&nbsp;</td></tr>";
			$this->row_count = 1;
			while ($show_invoice = $invoice_result->FetchRow())
			{
				$user_data = $this->get_user_data($db,$show_invoice["user_id"]);
				$this->body .= "<tr class=".$this->get_row_color()."><td class=medium_font>".$user_data["username"]." (".$user_data["firstname"]." ".$user_data["lastname"].")</td>";
				$this->body .= "<td class=medium_font>".date("M d,Y G:i - D",$show_invoice["invoice_date"])."</td>";
				$this->body .= "<td class=medium_font>".date("M d,Y G:i - D",$show_invoice["date_paid"])."</td>";
				//get invoice total
				$this->sql_query = "select sum(amount) as total_of_invoice from ".$this->balance_transactions." where invoice_id = ".$show_invoice["invoice_id"];
				if ($this->debug_invoices) echo $this->sql_query."<br>";
				$total_result = $db->Execute($this->sql_query);
				if (!$total_result)
				{
					echo $this->sql_query."<br>";
					echo "error getting earliest transaction";
					return false;
				}
				elseif ($total_result->RecordCount() == 1)
				{
					$show_total = $total_result->FetchRow();
					$invoice_total = $show_total["total_of_invoice"];
				}
				else
				{
					echo $this->sql_query."<br>";
					echo "error getting number of  transaction";
				}

				$this->body .= "<td><div nowrap><span class=medium_font>".$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$invoice_total)." ".$this->configuration_data["postcurrency"]."</span></div></td>";
				//echo "<td class=medium_font>".urldecode($show_invoice["payment_note"])."&nbsp;</td>";
				$this->body .= "<td><a href=index.php?a=78&z=1&b=".$show_invoice["invoice_id"]."&c=".$show_invoice["user_id"]."><span class=medium_font>mark unpaid</span></a></td>";
				$this->body .= "<td><a href=index.php?a=78&z=8&b=".$show_invoice["invoice_id"]."><span class=medium_font>details</span></a></td></tr>";
				$this->row_count++;
			} //end of while
			$this->body .= "</table></td></tr>";
			$this->body .= "<tr>\n\t<td align=center valign=top>\n\t<a href=index.php?a=78><b>invoice home</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>";
			return true;
		}
		else
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
			$this->title = "Transactions > Paid Invoices";
			$this->body .= "<tr><td class=medium_font align=center><b>There are no paid invoices.</b><br><br><br></td></tr>";
			$this->body .= "<tr>\n\t<td align=center valign=top class=medium_font>\n\t<a href=index.php?a=78><b>invoice home</b></a>\n\t</td>\n</tr>\n";
			$this->body .= "</table>";
			return true;
		}

	} //end of function paid_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_invoice ($db,$invoice_id=0)
	{
		//show an individual invoice
		$this->get_configuration_data($db);
		if ($invoice_id)
		{
			$this->sql_query = "select * from  ".$this->invoices_table." where invoice_id = ".$invoice_id;
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$invoice_result = $db->Execute($this->sql_query);
			if (!$invoice_result)
			{
				return false;
			}
			elseif ($invoice_result->RecordCount() == 1)
			{
				$show_invoice = $invoice_result->FetchRow();
				$user_data = $this->get_user_data($db,$show_invoice["user_id"]);
				//show the specifics of this invoice
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
				$this->title .= "Invoice Detail";
				//echo "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\tBelow are the details of this invoice \n\t</td>\n</tr>\n";
				$this->body .= "<tr class=row_color1><td class=medium_font>Invoice #</td><td class=medium_font>".$show_invoice["invoice_id"]."</td></tr>";
				$this->body .= "<tr class=row_color2><td class=medium_font>Date Invoiced</td><td class=medium_font>".date("M d,Y",$show_invoice["invoice_date"])."</td></tr>";
				$this->body .= "<tr><td class=medium_font>date paid</td>";
				if ($show_invoice["date_paid"])
					$this->body .=  "<td class=medium_font>".date("M d,Y",$show_invoice["date_paid"])." <a href=index.php?a=78&z=1&b=".$invoice_id."><span class=medium_font>( click to mark as unpaid )</span></a></td></tr>";
				else
					$this->body .= "<td class=medium_font>unpaid <a href=index.php?a=78&z=2&b=".$invoice_id."><span class=medium_font>( click to mark as paid )</span></a></td></tr>";
				$this->body .= "<tr class=row_color2><td class=medium_font>Invoice total</td><td class=medium_font>".$this->configuration_data["precurrency"]." ".
					$this->get_invoice_total($db,$show_invoice["invoice_id"])." ".$this->configuration_data["postcurrency"]."</td></tr>";
				$this->body .= "<tr><td class=medium_font>user id</td><td class=medium_font>".$user_data["username"]." ( ".$user_data["firstname"]." ".$user_data["lastname"]." )</td></tr>";
				$this->body .= "<tr class=row_color2><td class=medium_font>address</td><td class=medium_font>".$user_data["address"];
				if (strlen(trim($user_data["address_2"])) > 0)
					$this->body .= "<br>".$user_data["address_2"];
				$this->body .= "<Br>".$user_data["city"]." ".$user_data["state"]." ".$user_data["zip"]." ".$user_data["country"]."</td></tr>";
				$this->body .= "<tr><td class=medium_font>phone</td><td class=medium_font>".$user_data["phone"]."</td></tr>";

				$this->body .= "<tr class=row_color_red><td colspan=2 class=medium_font_light>Transactions in invoice</td></tr>";
				$this->sql_query = "select * from  ".$this->balance_transactions.", ".$this->classifieds_table." where  invoice_id = ".$invoice_id." and id = ad_id";
				if ($this->debug_invoices) echo $this->sql_query."<br>";
				$invoice_items_result = $db->Execute($this->sql_query);
				if (!$invoice_items_result)
				{
					echo $this->sql_query."<br>";
					return false;
				}
				elseif ($invoice_items_result->RecordCount() > 0)
				{
					$this->row_count = 0;
					$this->body .= "<tr><td colspan=2><table width=100% cellspacing=1 cellpadding=2>";
					$this->body .= "<tr  class=row_color_black>";
					$this->body .= "<td class=medium_font_light>Listing id</td>";
					if($this->is_class_auctions())
						$this->body .= "<td class=medium_font_light>Type</td>";
					$this->body .= "<td class=medium_font_light>Date placed</td>";
					$this->body .= "<td class=medium_font_light>Title</td>";
					$this->body .= "<td class=medium_font_light>Charged for</td>";
					$this->body .= "<td class=medium_font_light>Amount</td>";
					$this->body .= "</tr>";
					while ($show_item = $invoice_items_result->FetchRow())
					{
						$classified_data = $this->get_classified_data($db,$show_item["ad_id"]);
						if (!$classified_data)
							$classified_data = $this->get_expired_classified_data($db,$show_item["ad_id"]);
						if (!$classified_data) continue;
						$this->body .= "<tr class=".$this->get_row_color().">";
						$this->body .= "<td valign=top class=medium_font>".$show_item["ad_id"]."</td>";
						if($this->is_class_auctions())
						{
							if($show_item['item_type'] == 1)
								$this->body .= "<td valign=top class=medium_font>Classified</td>";
							elseif($show_item['item_type'] == 2)
								$this->body .= "<td valign=top class=medium_font>Auction</td>";
						}
						$this->body .= "<td valign=top class=medium_font>".date("M d,Y",$classified_data["date"])."</td>";
						$this->body .= "<td valign=top class=medium_font>".urldecode($classified_data["title"])."</td>";
						$this->body .= "<td valign=top class=medium_font>";
						if ($show_item["renewal"])
							$this->body .= "renewal";
						else
							$this->body .= "placement";
						if (($show_item["bolding"]) || ($show_item["better_placement"]) || ($show_item["featured_ad"]) ||
							($show_item["featured_ad_2"]) || ($show_item["featured_ad_3"]) || ($show_item["featured_ad_4"]) ||
							($show_item["featured_ad_5"]) || ($show_item["attention_getter"]))
						{
							$this->body .= "<br><B>including extras:</B> <br>";
							if ($show_item["bolding"]) $this->body .= "&nbsp;&nbsp;+bolding<br>";
							if ($show_item["better_placement"]) $this->body .= "&nbsp;&nbsp;+better placement<br>";
							if ($show_item["featured_ad"]) $this->body .= "&nbsp;&nbsp;+featured level 1<br>";
							if ($show_item["featured_ad_2"]) $this->body .= "&nbsp;&nbsp;+featured level 2<br>";
							if ($show_item["featured_ad_3"]) $this->body .= "&nbsp;&nbsp;+featured level 3<br>";
							if ($show_item["featured_ad_4"]) $this->body .= "&nbsp;&nbsp;+featured level 4<br>";
							if ($show_item["featured_ad_5"]) $this->body .= "&nbsp;&nbsp;+featured level 5<br>";
							if ($show_item["attention_getter"]) $this->body .= "&nbsp;&nbsp;+attention getter<br>";
						}
						$this->body .= "</td><td valign=top class=medium_font>".$this->configuration_data["precurrency"]." ".$show_item["amount"]." ".
							$this->configuration_data["postcurrency"]."</td></tr>";
						$this->row_count++;
					}
					$this->body .= "</table></td></tr>";
				}
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=2>\n\t<a href=index.php?a=17&b=".$user_data["id"].">
					<span class=medium_font_light>To ".$user_data["username"]." User Data </a>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center colspan=2>\n\t<a href=index.php?a=78>
					<span class=medium_font><b>invoice home</b> </a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
				return true;
			}
			else
				return false;
		}
		else
		{
			return false;
		}

	} //end of function show_invoice

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function create_invoices($db,$user_id=0,$send_email=0)
	{
		//select all balance transactions without a cc_transaction_id
		//select distinct users where cc_transaction_id = 0
		//get total for each distinct user
		//insert into invoices table once for each user
		//get earliest and latest dates for transactions within group of balance transactions
		//use invoice id to insert each balance transaction selected for this user within the geodesic_invoices_transactions table
		//this does not include final fees and auction fees on auctions that have not ended yet
		$this->get_configuration_data($db);
		if (!$user_id)
		{
			$this->sql_query = "select distinct(user_id) from ".$this->balance_transactions." where ad_id != 0 and cc_transaction_id = 0 and invoice_id = 0";
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$user_result = $db->Execute($this->sql_query);
			if (!$user_result)
			{
				return false;
			}
			elseif ($user_result->RecordCount() ==0)
			{
				//there are no transactions to
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
				$this->title = "Transactions > Invoices";
				$this->description = "Below are the details of all invoices you have just created.";
				$this->body .= "<tr><td class=medium_font align=center><b>There are no transactions to invoice.</b><br><br></td></tr>";
				$this->body .= "<tr>\n\t<td align=center colspan=2>\n\t<a href=index.php?a=78>
					<span class=medium_font><b>invoice home</b></a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
				return true;

			}
			else
			{
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
				$this->title = "Transactions > Invoices";
				$this->description = "Below are the details of all invoices you have just created.";
				$this->body .= "<tr><td><table width=100% cellpadding=2 cellspacing=1 border=0>";
				$this->body .= "<tr class=row_color_black><td class=medium_font_light>user</td>";
				$this->body .= "<td class=medium_font_light>amount</td>";
				$this->body .= "<td class=medium_font_light>mark paid</td>";
				$this->body .= "<td class=medium_font_light>invoice emailed to</td>";
				$this->body .= "<td class=medium_font_light>&nbsp;</td>";
				$this->body .= "</tr>";
				while ($show_user = $user_result->FetchRow())
				{
					//check to see
					//insert into invoice table
					$this->sql_query = "insert into ".$this->invoices_table."
						(user_id,invoice_date) values (".$show_user["user_id"].",".$this->shifted_time().")";
					if ($this->debug_invoices) echo $this->sql_query."<br>";
					$insert_user_invoice_result = $db->Execute($this->sql_query);
					if (!$insert_user_invoice_result)
					{
						return false;
					}
					$invoice_id = $db->Insert_ID();

					$this->sql_query = "select * from ".$this->balance_transactions."  where
						cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$show_user["user_id"]." and ad_id != 0";
					if ($this->debug_invoices) echo $this->sql_query."<br>";
					$user_transaction_result = $db->Execute($this->sql_query);
					if (!$user_transaction_result)
					{
						return false;
					}
					elseif ($user_transaction_result->RecordCount() ==0)
					{
						//there are no transactions to
					}
					else
					{
						$total = 0;
						if ($this->debug_invoices) echo $user_transaction_result->RecordCount()." is the number of balance transactions for ".$show_user["user_id"]."<bR>";
						while ($show_transaction = $user_transaction_result->FetchRow())
						{
							//insert balance transaction into invoice items table
							//$this->sql_query = "insert into ".$this->invoices_ads_table."
							//	(invoice_id,transaction_id)
							//	values
							//	(".$invoice_id.",".$show_transaction["transaction_id"].")";
							//if ($this->debug_invoices) echo $this->sql_query."<br>";
							//$insert_user_invoice_item_result = $db->Execute($this->sql_query);
							//if (!$insert_user_invoice_item_result)
							//{
							//	return false;
							//}
							$total = $total + $show_transaction["amount"];
						} //end of while
					}

					$this->sql_query = "update ".$this->balance_transactions."  set
						invoice_id = ".$invoice_id."
						where cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$show_user["user_id"]." and ad_id != 0";
					if ($this->debug_invoices) echo $this->sql_query."<br>";
					$update_invoice_transaction_result = $db->Execute($this->sql_query);
					if (!$update_invoice_transaction_result)
					{
						return false;
					}


					$user_data = $this->get_user_data($db,$show_user["user_id"]);
					$this->body .= "<tr class=".$this->get_row_color()."><td class=medium_font>".
						$user_data["username"]." (".$user_data["firstname"]." ".$user_data["lastname"].")</td>";
					$this->body .= "<td class=medium_font>".$this->configuration_data["precurrency"]." ";
					if ($total > 0)
						$this->body .= $total;
					else
						$this->body .= "0";
					$this->body .=  " ".$this->configuration_data["postcurrency"]."</td>";
					$this->body .= "<td><a href=index.php?a=78&z=2&b=".$invoice_id."><span class=medium_font>mark paid</span></a></td>";
					//send an email invoice to this customer

					if ($total > 0)
					{
						$this->page_id = 177;
						$this->get_text($db);
						$additional = "-f".$this->configuration_data["site_email"];
						$subject = urldecode($this->messages[3098]).$invoice_id;
						$message = urldecode($this->messages[3099]).$user_data["firstname"]."\n\n";
						$message .= urldecode($this->messages[3100]).$this->configuration_data["precurrency"]." ".sprintf("%01.2f", $total)." ".$this->configuration_data["postcurrency"]."\n\n".urldecode($this->messages[3101])."\n\n";
						$message .= $this->configuration_data["classifieds_url"]."?a=30&b=".$invoice_id." \n\n";
						$message .= urldecode($this->messages[3102]);
						if ($this->debug_invoices)
							$this->body .= "sending email to ".$user_data["email"]." from ".$this->configuration_data["site_email"]." with this message - ".$message." - ".$this->configuration_data["email_configuration"]."<br>\n";
						if ($this->configuration_data["email_header_break"])
							$separator = "\n";
						else
							$separator = "\r\n";
						$from = "From: ".$this->configuration_data["site_email"].$separator."Reply-to: ".$this->configuration_data["site_email"].$separator;
						if ($this->configuration_data["email_configuration"] == 1)
							mail($user_data["email"],$subject,$message,$from,$additional);
						elseif ($this->configuration_data["email_configuration"] == 2)
							mail($user_data["email"],$subject,$message,$from);
						else
							mail($user_data["email"],$subject,$message);
						$this->body .= "<td class=medium_font>".$user_data["email"]." </td>";
					}
					else
						$this->body .= "<td class=medium_font>none sent</td>";

					$this->body .= "<td><a href=index.php?a=78&b=".$invoice_id."&z=8><span class=medium_font>details</span></a> </td>";
					$this->body .= "</tr>";
				} //end of while
				$this->body .= "</table></td></tr>";
				$this->body .= "<tr>\n\t<td align=center colspan=2>\n\t<a href=index.php?a=78>
					<span class=medium_font><b>invoice home</b> </a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
				return true;
			}
		}
		else
		{
			//create invoice for just this user
			$this->sql_query = "select * from ".$this->balance_transactions."
				where cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$user_id." and ad_id != 0";
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$user_transaction_result = $db->Execute($this->sql_query);
			if (!$user_transaction_result)
			{
				return false;
			}
			elseif ($user_transaction_result->RecordCount() ==0)
			{
				//there are no transactions to invoice for this user
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
				$this->title .= "Transactions > Invoices";
				$this->description .= "Below are the details of all invoices you have just created.";
				$this->body .= "<tr><td class=medium_font align=center><b>There are no transactions to invoice for this user.</b></td></tr>";
				$this->body .= "<tr>\n\t<td align=center colspan=2>\n\t<a href=index.php?a=78>
					<span class=medium_font><b>invoice home</b></a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";
				return true;
			}
			else
			{
				$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
				$this->title .= "<tr class=row_color_red>\n\t<td colspan=2 class=very_large_font_light>\n\t<b>Transactions > Invoices</b> \n\t</td>\n</tr>\n";
				$this->description .= "<tr class=row_color_red>\n\t<td colspan=2 class=medium_font_light>\n\tBelow are the details of invoices you just created. \n\t</td>\n</tr>\n";
				//insert into invoice table
				$this->sql_query = "insert into ".$this->invoices_table."
					(user_id,invoice_date) values (".$user_id.",".$this->shifted_time().")";
				if ($this->debug_invoices) echo $this->sql_query."<br>";
				$insert_user_invoice_result = $db->Execute($this->sql_query);
				if (!$insert_user_invoice_result)
				{
					return false;
				}
				$invoice_id = $db->Insert_ID();

				$total = 0;
				while ($show_transaction = $user_transaction_result->FetchRow())
				{
					//insert balance transaction into invoice items table
					//$this->sql_query = "insert into ".$this->invoices_ads_table."
					//	(invoice_id,transaction_id)
					//	values
					//	(".$invoice_id.",".$show_transaction["transaction_id"].")";
					//if ($this->debug_invoices) echo $this->sql_query."<br>";
					//$insert_user_invoice_item_result = $db->Execute($this->sql_query);
					//if (!$insert_user_invoice_item_result)
					//{
					//	return false;
					//}
					$total = $total + $show_transaction["amount"];
				} //end of while

				$this->sql_query = "update ".$this->balance_transactions."  set
					invoice_id = ".$invoice_id."
					where cc_transaction_id = 0 and invoice_id = 0 and user_id = ".$user_id." and ad_id != 0";
				if ($this->debug_invoices) echo $this->sql_query."<br>";
				$update_invoice_transaction_result = $db->Execute($this->sql_query);
				if (!$update_invoice_transaction_result)
				{
					return false;
				}

				//send an email invoice to this customer
				$user_data = $this->get_user_data($db,$user_id);
				$this->body .= "<tr><td class=medium_font>user</td><td class=medium_font>".$user_data["username"]." (".$user_data["firstname"]." ".$user_data["lastname"].")</td></tr>";
				$this->body .= "<tr><td class=medium_font>amount of invoice</td><td class=medium_font>".$this->configuration_data["precurrency"]." ".$total." ".$this->configuration_data["postcurrency"]."</td></tr>";
				$this->body .= "<tr><td colspan=2><a href=index.php?a=78&z=2&b=".$invoice_id."><span class=medium_font>mark paid</span></a></td></tr>";
				$this->body .= "<tr><td colspan=2><a href=index.php?a=78&z=8&b=".$invoice_id."><span class=medium_font>details</span></a></td></tr>";
				$this->body .= "<tr class=row_color_black>\n\t<td align=center colspan=2>\n\t<a href=index.php?a=78>
					<span class=medium_font_light>invoice home </a>\n\t</td>\n</tr>\n";
				$this->body .= "</table>";

				if ($total > 0)
				{
					$this->page_id = 177;
					$this->get_text($db);
					$additional = "-f".$this->configuration_data["site_email"];
					$subject = urldecode($this->messages[3098]).$invoice_id;
					$message = urldecode($this->messages[3099]).$user_data["firstname"]."\n\n";
					$message .= urldecode($this->messages[3100]).$total."\n\n".urldecode($this->messages[3101])."\n\n";
					$message .= $this->configuration_data["classifieds_url"]."?a=30&b=".$invoice_id." \n\n";
					$message .= urldecode($this->messages[3102]);
					if ($this->configuration_data["email_header_break"])
						$separator = "\n";
					else
						$separator = "\r\n";
					$from = "From: ".$this->configuration_data["site_email"].$separator."Reply-to: ".$this->configuration_data["site_email"].$separator;
					if ($this->configuration_data["email_configuration"] == 1)
						mail($user_data["email"],$subject,$message,$from,$additional);
					elseif ($this->configuration_data["email_configuration"] == 2)
						mail($user_data["email"],$subject,$message,$from);
					else
						mail($user_data["email"],$subject,$message);
				}
				return true;
			}
		}

	} //end of function create_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function mark_invoice_paid($db,$invoice_id=0,$user_id=0)
	{
		if ($invoice_id && $user_id)
		{
			$this->sql_query = "UPDATE ".$this->invoices_table." SET date_paid = ".$this->shifted_time()." WHERE
				invoice_id = ".$invoice_id." AND
				user_id = ".$user_id;
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$paid_result = $db->Execute($this->sql_query);
			if (!$paid_result)
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
			return false;
		}

	} //end of function mark_invoice_paid

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function mark_invoice_unpaid($db,$invoice_id=0,$user_id=0)
	{
		if ($invoice_id && $user_id)
		{
			$this->sql_query = "UPDATE ".$this->invoices_table." SET date_paid = 0 WHERE
				invoice_id = ".$invoice_id." AND
				user_id = ".$user_id;
			if ($this->debug_invoices) echo $this->sql_query."<br>";
			$unpaid_result = $db->Execute($this->sql_query);
			if (!$unpaid_result)
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
			return false;
		}
	} //end of function mark_invoice_unpaid

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_invoice_total($db,$invoice_id=0)
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
				while ($show_item = $invoice_item_result->FetchRow())
				{
					$total = $total + $show_item["amount"];
				} // end of while
				return $total;
			}
		}
		else
			return false;
	} // end of function get_invoice_total

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_invoice_cutoff($db,$invoice_cutoff=0)
	{
		$this->sql_query = "update ".$this->site_configuration_table." set
			invoice_cutoff = ".$invoice_cutoff;
		if ($this->debug_invoices) echo $this->sql_query."<br>";
		$invoice_cutoff_result = $db->Execute($this->sql_query);
		if (!$invoice_cutoff_result)
		{
			return false;
		}
		else
			return true;
	} //end of function update_invoice_cutoff

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function invoice_cutoff_form($db,$invoice_id=0)
	{
		$this->get_configuration_data($db);
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=78&z=5 method=post>";
		$this->body .= "<table cellpadding=3 cellspacing=0 border=0 align=center class=row_color1 width=100%>\n";
		$this->title = "Transactions > Invoice Cutoff";
		$this->description = "This form allows you to set a
			time limit in the number of days after an invoice is created for a users account to remain live.  Once this limit has been reached
			and the invoice remains unpaid the user will not be able to place a new listing, renew a current/expired listing or upgrade a current listing on the site.";
		$this->body .= "<tr>\n\t<td class=medium_font width=60% align=center>\n\tNumber of days to have an Unpaid Invoice before the account is cutoff: \n\t</td>\n";
		$this->body .= "<td align=left><select name=b>";
		for ($i=0;$i < 181;$i++)
		{
			$this->body .= "<option ";
			if ($this->configuration_data["invoice_cutoff"] == $i)
				$this->body .= "selected";
			$this->body .= ">".$i."</option>";
		}
		$this->body .= "</select></td></tr>\n";
		if (!$this->admin_demo()) $this->body .= "<tr><td colspan=2 class=medium_font align=center><input type=submit value=\"Save\"></td></tr>";
		$this->body .= "</table></form>";
		return true;

	} // end of function invoice_cutoff_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function search_invoices($db,$invoice_id=0)
	{
		if ($invoice_id)
		{
			$display_invoice_result = $this->show_invoice($db,$invoice_id);
			if (!$display_invoice_result)
			{
				//display that there was no result
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=78&z=4 method=post>";
				$this->body .= "<table width=100%>";
				$this->title = "Invoice Search";
				$this->body .= "<tr class=row_color2><td class=large_font>no invoice exists by that id...please try again</td>";
				$this->body .= "<tr class=row_color2><td class=medium_font>enter an invoice id <input type=text name=b></td>";
				if (!$this->admin_demo()) $this->body .= "<td class=medium_font><input type=submit value=\"Submit\"></td>"; 
				$this->body .= "</tr>";
				$this->body .= "</table>";
				return true;
			}
			else
			{
				//the invoice was displayed
				return true;
			}
		}
		else
		{
			//no invoice id to check
			return false;
		}
	} //function search_invoices

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function charge_select_box($present_value=0,$name=0)
	{
		if ($name)
		{
			$body = "";

			if (strchr($present_value,"."))
			{
				$split_value = explode(".",$present_value);
				$dollars = $split_value[0];
				$cents = $split_value[1];
			}
			else
			{
				$dollars = $present_value;
				$cents = 0;
			}

			$body .= "$<select name=\"".$name."[0]\">\n\t\t";
			for ($i=0;$i<1000;$i++)
			{
				$body .= "<option ";
				if ($i == $dollars)
					$body .= "selected ";
				$body .= " value=".$i.">".$i."</option>\n\t\t";
			}
			$body .= "</select>\n\t";
			$body .= ".<select name=\"".$name."[1]\">\n\t\t";
			for ($i=0;$i<100;$i++)
			{
				$body .= "<option ";
				if ($i == $cents)
					$body .= "selected";
				$body .= ">".sprintf("%02d",$i)."</option>\n\t\t";
			}
			$body .= "</select>\n\t";

			return $body;
		}
		else
		{
			return false;
		}
	} //end of function charge_select_box

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_max_invoice($db, $limits)
	{
		foreach($limits as $index => $value)
		{
			$this->sql_query = "update ".$this->price_plan_table." set invoice_max = ".$value[0].".".sprintf("%02d",$value[1])." where price_plan_id = ".$index;
			//echo $this->sql_query.'<Br>';
			$result = $db->Execute($this->sql_query);
			if(!$result)
				return false;
		}

		return true;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
} //end of class Admin_invoicing
?>
