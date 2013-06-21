<? //admin_transaction_management_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Transaction_management extends Admin_site{

	var $debug_transactions = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Transaction_management($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function transaction_home($db)
	{
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
		$this->title = "Transactions > Search Transactions";
		$this->description = "Use this page to view your transactions history and other transaction related statistics.";

		//stats
		//transaction statistics by month
		//echo "<tr class=row_color2>\n\t<td class=medium_font>\n\t<b>view transactions by month:</b><br>";
		//$this->view_transactions_by_month_form($db);
		//echo " \n\t</td>\n</tr>\n";

		//amount by month
		$this->body .= "<tr class=row_color2>\n\t<td class=medium_font>\n\t<b>view transactions by month:</b><br>";
		$this->view_transactions_by_month_form($db);
		$this->body .= " \n\t</td>\n</tr>\n";

		//transactions
		//last # of transactions by type

		//search transactions by date
		$this->body .= "<tr class=row_color2>\n\t<td class=medium_font>\n\t<b>view transactions within date range:</b><br>";
		$this->search_by_date_box();
		$this->body .= " \n\t</td>\n</tr>\n";

		//search transactions by user
		//link to search user
		$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=16><span class=medium_font>find user to view transactions</span></a>\n\t</td>\n</tr>\n";

		//unapproved ads
		//link to unapproved ads
		$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=40&z=1><span class=medium_font>unapproved transactions</span></a>\n\t</td>\n</tr>\n";


		//link to invoice system
		$this->body .= "<tr>\n\t<td align=center>\n\t<a href=index.php?a=78><span class=medium_font>invoicing</span></a>\n\t</td>\n</tr>\n";

		//search transactions by category

		$this->body .= "</table>\n";
		return true;

	} //end of transaction_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function unapproved_transactions_list($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "Text[1] = [\"HTTPS Required\", \"To view the entire credit card number, you must view this page using HTTPS.  This is for the protection of the user's credit card information.  Click the \\\"View Full Credit Card Number\\\" link to the right to view the full credit card number.\"]\n";
		//".$this->show_tooltip(1,1)."
		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";
		$this->get_configuration_data($db);
		$this->sql_query = "select * from ".$this->classifieds_table." where (live = 0 and ends > ".$this->shifted_time()." and customer_approved = 1) or (renewal_payment_expected != 0) or (live = 2) order by date asc";
		$result = $db->Execute($this->sql_query);
		if ($this->debug_transactions) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
			$this->title = "Transactions > Unapproved Transactions";
			$this->description = "Below is the list of
				transactions purchased by check,cash,money order or other incomplete transaction device and awaiting your approval.
				When you receive payment you can approve the listing
				and it will go live.  Old listings will be automatically deleted after 30 days.";
			$this->body .= "<tr bgcolor=000066><td colspan=10 class=medium_font_light align=center><b>Listings Awaiting Approval</b></td></tr>\n\t";
			$this->body .= "<tr class=row_color_black>\n\t";
			//echo "<td class=medium_font_light>name </td>\n\t";
			if($this->is_class_auctions())
				$this->body .= "<td class=medium_font_light><b>type</b></td>\n\t";
			$this->body .= "<td class=medium_font_light><b>user</b></td>\n\t";
			$this->body .= "<td class=medium_font_light><b>title</b></td>\n\t";
			$this->body .= "<td class=medium_font_light><b>date</b></td>\n\t";
			$this->body .= "<td class=medium_font_light align=center><b>need<br>approval</b></td>\n\t";
			$this->body .= "<td class=medium_font_light align=center><b>need<BR>payment</b></td>\n\t";
			$this->body .= "<td class=medium_font_light align=center><b>renewal<BR>payment<br>expected</b></td>\n\t";
			$this->body .= "<td class=medium_font_light></td>\n\t";
			$this->body .= "<td class=medium_font_light></td>\n\t";
			$this->body .= "<td class=medium_font_light></td></tr>\n";

			$this->row_count = 0;
			while ($show = $result->FetchRow())
			{
				$user_data = $this->get_user_data($db,$show["seller"]);

				$this->body .= "<tr class=".$this->get_row_color().">\n\t";
				if($this->is_class_auctions())
				{
					if($show['item_type'] == 1)
						$this->body .= "<td class=medium_font align=center>Classified Ad</td>\n\t";
					elseif($show['item_type'] == 2)
						$this->body .= "<td class=medium_font align=center>Auction</td>\n\t";
				}
				$this->body .= "<td class=medium_font>".$user_data["username"]."<br>";
				$this->body .= "<span class=small_font>".stripslashes($user_data["lastname"]).",".stripslashes($user_data["firstname"])."
					</span></td>\n\t";
				$this->body .= "<td class=small_font>".stripslashes(urldecode($show["title"]))."(".$show["id"].")</td>\n\t";
				$this->body .= "<td class=small_font>".date("M d, Y H:i:s", $show["date"])."</td>\n\t";

				$this->body .= "<td align=center>";
				if ($show["live"] == 2) $this->body .= "<span class=medium_font align=center><b>X</b></span>";
				$this->body .= "&nbsp; </td>\n\t";

				$this->body .= "<td align=center>";
				if (($show["transaction_type"] == 1) || ($show["transaction_type"] == 4) || ($show["transaction_type"] == 5))
					$this->body .= "<span class=medium_font align=center><b>X</b></span>";
				$this->body .= "&nbsp; </td>\n\t";
				$this->body .= "<td align=center>";
				if (($show["renewal_payment_expected"] == 1) || ($show["renewal_payment_expected"] == 2))
					$this->body .= "<span class=medium_font align=center><b>X</b></span>";
				$this->body .= "&nbsp; </td>\n\t";

				$this->body .= "<td><a href=index.php?a=40&z=3&b=".$show["id"]."><span class=medium_font><img src=admin_images/btn_admin_approve.gif alt=approve border=0></span></a></td>\n\t";

				$this->body .= "<td><a href=index.php?a=40&z=2&b=".$show["id"]."><span class=medium_font><img src=admin_images/btn_admin_view.gif alt=view border=0></span></a></td>\n\t";
				if (($show["renewal_payment_expected"] == 1) || ($show["renewal_payment_expected"] == 2))
					$this->body .= "<td><a href=index.php?a=40&z=5&b=".$show["id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>\n</tr>\n";
				else
					$this->body .= "<td><a href=index.php?a=40&z=4&b=".$show["id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>\n</tr>\n";
				$this->row_count++;
			}
			$this->body .= "</table>\n";
		}
		else
		{
			//no unapproved transactions
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
			$this->title = "Transactions > Unapproved Transactions";
			$this->description = "Below is the list of
				transactions purchased by check, cash, money order or other incomplete transaction device awaiting your approval.
				When you receive payment you can approve the listing and it will go live.  Old listings will be automatically deleted after 30 days.";
			$this->body .= "<tr>\n\t<td colspan=6 class=medium_font align=center>\n\t
				<br><br><b>There are no listings awaiting your approval.</b><br><br> \n\t</td>\n</tr>\n";
			$this->body .= "</table>\n";
		}

		$this->sql_query = "select * from ".$this->classified_subscription_holds_table;
		$hold_result = $db->Execute($this->sql_query);
		if ($this->debug_transactions) echo $this->sql_query."<br>\n";
		if (!$hold_result)
		{
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($hold_result->RecordCount() > 0)
		{
			$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
			$this->body .= "<tr bgcolor=000066>\n\t<td colspan=8 class=medium_font_light>\n\t<b>Unapproved Subscription Renewals</b> \n\t</td>\n</tr>\n";
			$this->description = "Below is the list of
				subscription renewal transactions purchased by check,cash or money order.  When you receive payment you can
				approve the subscription renewal request renewing the subscription.";
			$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light><b>username/name</b> </td>\n\t";
			//echo "<td class=medium_font_light><b>name</b></td>\n\t";
			$this->body .= "<td class=medium_font_light><b>date entered</b></td>\n\t";
			$this->body .= "<td class=medium_font_light><b>current subscription expires</b></td>\n\t";
			$this->body .= "<td class=medium_font_light><b>renew</b></td>\n\t";
			$this->body .= "<td class=medium_font_light><b>delete</b></td></tr>\n";

			$this->row_count = 0;
			while ($show = $hold_result->FetchRow())
			{
				$user_data = $this->get_user_data($db,$show["user_id"]);
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>".$user_data["username"]."<br>";
				$this->body .= "<span class=small_font>".stripslashes($user_data["lastname"]).",".stripslashes($user_data["firstname"])."</span></td>\n\t";
				$this->body .= "<td align=center class=small_font>\n\t".date("M d, Y H:i:s", $show["date"])." </td>\n\t";
				$this->sql_query = "select * from ".$this->classified_user_subscriptions_table." where user_id = ".$show["user_id"];
				$get_subscriptions_results = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$get_subscriptions_results)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					return false;
				}
				elseif ($get_subscriptions_results->RecordCount() == 1)
				{
					$show_subscription = $get_subscriptions_results->FetchRow();
					$expire_date = date("M d, Y H:i:s", $show_subscription["subscription_expire"]);
				}
				else
				{
					$expire_date = "expired";
				}
				$this->body .= "<td align=center class=small_font>\n\t".$expire_date." </td>\n\t";
				$this->body .= "<td><a href=index.php?a=71&z=1&b=".$show["renewal_id"]."><span class=medium_font>renew listing</a></span></td>\n\t";
				$this->body .= "<td><a href=index.php?a=71&z=2&b=".$show["renewal_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>\n</tr>\n";
				$this->row_count++;
			}
			$this->body .= "</table>\n";
		}

		$this->sql_query = "SELECT cc_id,name,cc_transaction_table FROM $this->cc_choices WHERE chosen_cc = 1";
		$cc_result = $db->Execute($this->sql_query);
		if ($cc_result->RecordCount() == 1)
			$chosen_cc = $cc_result->FetchRow();
		else
			return false;

		//get unapproved account balance purchases
		if (($this->configuration_data["use_account_balance"]) && ($this->configuration_data["positive_balances_only"]))
		{
			$this->sql_query = "select * from ".$this->balance_transactions." where approved = 0 and cc_transaction_id < 99999999 and invoice_id < 99999999";
			$balance_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$balance_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($balance_result->RecordCount() > 0)
			{
				$this->body .= "
					<table cellpadding=4 cellspacing=1 border=0 align=center class=row_color1 width=100%>
					    <tr bgcolor=000066>
					    	<td colspan=8 align=center class=medium_font_light><b>Site Balance Transactions Awaiting Approval</b></td>
					    </tr>
						<tr class=row_color_black>
							<td class=medium_font_light><b>user</b></td>
							<td class=medium_font_light align=center><b>transaction<br>date</b></td>
							<td class=medium_font_light align=center><b>amount<br>added</b></td>
							<td class=medium_font_light align=center><b>credit card<br>number</b> ".$this->show_tooltip(1,1)."</td>
							<td class=medium_font_light align=center><b>current<br>balance</b></td>
							<td class=medium_font_light align=center><b>view user</b></td>
							<td class=medium_font_light></td>
							<td class=medium_font_light></td>
						</tr>";
				$this->sql_query = "SELECT * FROM $this->cc_choices WHERE chosen_cc = 1";
				$cc_result = $db->Execute($this->sql_query);
				if ($cc_result->RecordCount() == 1) $chosen_cc = $cc_result->FetchRow();
				else return false;

				switch ($chosen_cc["cc_id"])
				{
					case 1:
						//authorize.net
						$trans_table_key = "authorizenet_transaction_id";
						break;
					case 2:
						//2checkout
						$trans_table_key = "transaction_id";
						break;
					case 3:
						//bitel
						$trans_table_key = "bitel_transaction_id";
					break;
					case 4:
						//linkpoint
						$trans_table_key = "linkpoint_transaction_id";
						break;
					case 5:
						//internetsecure
						$trans_table_key = "internetsecure_transaction_id";
						break;
					case 6:
						//payflow pro
						$trans_table_key = "payflow_pro_transaction_id";
						break;
					case 7:
						//paypal pro
						$trans_table_key = "transaction_id";
						break;
					case 8:
						//add new cc payment handler here
					break;
					case 9:
						//manual processing
						$trans_table_key = "manual_transaction_id";
					break;
				}
				$this->row_count = 0;
				while ($show = $balance_result->FetchRow())
				{
					$user_data = $this->get_user_data($db,$show["user_id"]);
					$this->body .= "
						<tr class=".$this->get_row_color().">
							<td class=medium_font>
								".$user_data["username"]."<br>
								<span class=small_font>".stripslashes($user_data["lastname"]).",".stripslashes($user_data["firstname"])."</span>
							</td>
							<td align=center class=small_font>".date("M d, Y H:i:s", $show["date"])."</td>
							<td align=center class=small_font>\n\t".sprintf("%01.2f",$show["amount"])."</td>";
					$this->sql_query = "SELECT * FROM ".$chosen_cc["cc_transaction_table"]." WHERE account_balance = ".$show["transaction_id"]." ORDER BY $trans_table_key DESC LIMIT 1";
					$card_data = $db->Execute($this->sql_query);
					if ($card_data !== false)
					{
						if ($card_data->RecordCount() == 1)
						{
							$card = $card_data->FetchRow();
							require_once '../classes/site_class.php';
							$cc_number = Site::decrypt($card["card_num"], $card["decryption_key"]);
							$cvv2 = $card["cvv2_code"];
							if ($_SERVER["HTTPS"] != "on")
							{
								$cc_num_Xs = '';$cvv2_Xs = '';
								for ($i=0;$i<strlen($cc_number);$i++)
									$cc_num_Xs .= 'X';
								for ($i=0;$i<strlen($cvv2);$i++)
									$cvv2_Xs .= 'X';
								$cc_number = substr_replace($cc_number,$cc_num_Xs,0,-4);
								$cvv2 = $cvv2_Xs;
							}
							$this->body .= "<td align=center class=small_font>".$chosen_cc["name"].
								"<br>".chunk_split($cc_number,4,' ')."
								<br>Expiration Date - ".$card["exp_date"];
							if ($chosen_cc["cc_transaction_table"]=='geodesic_cc_paypal_transactions' ||
								$chosen_cc["cc_transaction_table"]=='geodesic_cc_manual_transactions')
								$this->body .= "<br>Card Verification Code - $cvv2";
							$this->body .= "</td>";
						}
						else
							$this->body .= "
							<td align=center class=small_font><i>not available<br>check database</i></td>";
					}
					else
						$this->body .= "
							<td align=center class=small_font><i>not available<br>check database</i></td>";
					$this->body .= "
							<td align=center class=small_font>\n\t".sprintf("%01.2f",$user_data["account_balance"])."</td>
							<td align=center><a href=index.php?a=17&b=".$show["user_id"]."><span class=medium_font><img src=admin_images/btn_admin_view.gif alt=view user border=0></a></span></td>
							<td align=center><a href=index.php?a=77&z=1&b=".$show["transaction_id"]."><span class=medium_font><img src=admin_images/btn_admin_approve.gif alt=approve border=0></a></span></td>
							<td align=center><a href=index.php?a=77&z=2&b=".$show["transaction_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>
						</tr>";
					$this->row_count++;
				}
				$this->body .= "</table>\n";
			}
			else
			{
				//no unapproved balance transactions
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
				$this->title = "Transactions > Unapproved Transactions";
				$this->description = "Below is the list of
					transactions purchased by check, cash, money order or other incomplete transaction device awaiting your approval.
					When you receive payment you can approve the listing and it will go live.  Old listings will be automatically deleted after 30 days.";
				$this->body .= "<tr>\n\t<td colspan=6 class=medium_font align=center>\n\t
					<br><br><b>There are no Site Balance transactions awaiting your approval.</b><br> \n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
			}
		}

		if (($chosen_cc["cc_id"]==9) && ($this->configuration_data["use_account_balance"]) && (!$this->configuration_data["positive_balances_only"]))
		{
			//UPDATE BALANCE TRANSACTIONS TABLE TO REFLECT INVOICES TABLE
			$this->sql_query = "SELECT date_paid,invoice_id FROM $this->invoices_table";
			$invoice_table_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$invoice_table_result)
				return false;
			if ($invoice_table_result->RecordCount() > 0)
			{
				while ($invoices = $invoice_table_result->FetchRow())
				{
					if ($invoices["date_paid"] > 0)
						$this->sql_query = "UPDATE $this->balance_transactions SET approved = 1 WHERE invoice_id = ".$invoices["invoice_id"];
					else
						$this->sql_query = "UPDATE $this->balance_transactions SET approved = 0 WHERE invoice_id = ".$invoices["invoice_id"];
					$update_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$update_result)
						return false;
				}
			}

			//GET UNPAID INVOICES IF MANUAL CC HANDLER IS SELECTED
			$this->sql_query = "
				SELECT * FROM ".$chosen_cc["cc_transaction_table"]." INNER JOIN $this->balance_transactions
					ON
						pay_invoice = invoice_id
					WHERE
						invoice_id > 0 AND
						approved = 0";
			$invoice_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$invoice_result)
				return false;
			elseif ($invoice_result->RecordCount() > 0)
			{
				// Invoice Transactions
				$this->body .="
					<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>
							<tr bgcolor=000066>
						    	<td colspan=8 align=center class=medium_font_light><b>Balance Transactions Awaiting Approval</b></td>
						    </tr>
							<tr class=row_color_black>
								<td class=medium_font_light>username/name </td>
								<td class=medium_font_light>date entered </td>
								<td class=medium_font_light>amount</td>
								<td class=medium_font_light>credit card number ".$this->show_tooltip(1,1)."</td>
								<td class=medium_font_light>view user </td>
								<td class=medium_font_light>approve </td>
								<td class=medium_font_light>delete </td>
							</tr>";
				$this->row_count = 0;
				while ($show = $invoice_result->FetchRow())
				{
					$user_data = $this->get_user_data($db,$show["user_id"]);
					$this->body .= "
						<tr class=".$this->get_row_color().">
							<td class=medium_font>
								".$user_data["username"]."<br>
								<span class=small_font>".stripslashes($user_data["lastname"]).",".stripslashes($user_data["firstname"])."</span>
							</td>
							<td align=center class=small_font>".date("M d, Y H:i:s", $show["date"])."</td>
							<td align=center class=small_font>\n\t".sprintf("%01.2f",$show["amount"])."</td>";

					if ($show["card_num"] && $show["decryption_key"])
					{
						require_once '../classes/site_class.php';
						$cc_number = Site::decrypt($show["card_num"], $show["decryption_key"]);
						$cvv2 = $card["cvv2_code"];
						if ($_SERVER["HTTPS"] != "on")
						{
							$cc_num_Xs = '';$cvv2_Xs = '';
							for ($i=0;$i<strlen($cc_number);$i++)
								$cc_num_Xs .= 'X';
							for ($i=0;$i<strlen($cvv2);$i++)
								$cvv2_Xs .= 'X';
							$cc_number = substr_replace($cc_number,$cc_num_Xs,0,-4);
							$cvv2 = $cvv2_Xs;
						}
						$this->body .= "<td align=center class=medium_font>".$chosen_cc["name"].
							"<br>".chunk_split($cc_number,4,' ')."
							<br>Expiration Date - ".$show["exp_date"];
						if ($chosen_cc["cc_transaction_table"]=='geodesic_cc_paypal_transactions' ||
							$chosen_cc["cc_transaction_table"]=='geodesic_cc_manual_transactions')
							$this->body .= "<br>Card Verification Code - $cvv2";
						$this->body .= "</td>";
					}
					else
						$this->body .= "
							<td align=center class=small_font><i>not available</i></td>";
					$this->body .= "
							<td><a href=index.php?a=17&b=".$show["user_id"]."><span class=medium_font>view user</a></span></td>
							<td><a href=index.php?a=77&z=1&b=".$show["transaction_id"]."><span class=medium_font><img src=admin_images/btn_admin_approve.gif alt=approve border=0></a></span></td>
							<td><a href=index.php?a=77&z=2&b=".$show["transaction_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>
						</tr>";
					$this->row_count++;
				}
				$this->body .= "</table>\n";
			}
			else
			{
				//no unapproved balance transactions
				$this->body .= "<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>\n";
				$this->title = "Transactions > Unapproved Transactions";
				$this->description = "Below is the list of
					transactions purchased by check, cash, money order or other incomplete transaction device awaiting your approval.
					When you receive payment you can approve the listing and it will go live.  Old listings will be automatically deleted after 30 days.";
				$this->body .= "<tr>\n\t<td colspan=6 class=medium_font align=center>\n\t
					<b>There are no invoice payment transactions awaiting your approval.</b><br><br><br> \n\t</td>\n</tr>\n";
				$this->body .= "</table>\n";
			}
		}

			/*//show current unpaid invoices
			$this->sql_query = "select * from ".$this->invoices_table." where date_paid = 0 and user_id = ".$this->classified_user_id." order by invoice_date asc";
			$this->body .= "<tr class=".$css_tag."><td>".$show_invoice["invoice_id"]."</td>";
			$this->body .= "<td>".date("M d,Y",$show_invoice["invoice_date"])."</td>";
			$this->body .= "<td>".$this->configuration_data["precurrency"]." ".
				sprintf("%01.2f",$this->get_invoice_total($db,$show_invoice["invoice_id"]))." ".$this->configuration_data["postcurrency"]."</td>";
		*/
			return true;
	} //end of function unapproved_transactions_list

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function view_unapproved_transaction($db,$transaction_id=0)
	{
		if ($transaction_id)
		{
			$this->sql_query = "select * from ".$this->site_configuration_table;
			$result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$this->configuration_data = $result->FetchRow();

			}
			$ad = $this->display_basic_classified_data($db,$transaction_id);
			if ($ad)
			{
				$this->title = "Transactions > Unapproved Transactions > View";
				$this->description = "Displayed below is a transaction needing your approval.  If you wish to make the listing go live click the \"approve transaction\" button below.";

				$this->body .= "
					<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>
						<tr>
							<td>".$ad."</td>
						</tr>
						<tr>
							<td align=center>
								<a href=index.php?a=40&z=3&b=".$transaction_id."><span class=medium_font><b>Approve Transaction</b></span></a><br>
								<a href=index.php?a=40&z=1><span class=medium_font><b>back to Unapproved Transactions</b></span></a>
							</td>
						</tr>
					</table>\n";

				return true;
			}
			else
				return false;
		}
		else
		{
			return false;
		}
	} //end of function view_unapproved_transaction

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function approve_transaction($db,$transaction_id=0)
	{
		if ($transaction_id)
		{
			$this->get_ad_configuration($db);

			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$transaction_id;
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$ad_result = $db->Execute($this->sql_query);
			if (!$ad_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				exit;
			}
			$show = $ad_result->FetchRow();
			if ($this->debug_transactions)
			{
				echo $show["renewal_payment_expected"]." is RENEWAL_PAYMENT_EXPECTED<bR>\n";
				echo $show["renewal_length"]." is RENEWAL_LENGTH<BR>\n";
				echo $show["renewal_better_placement"]." is RENEWAL_BETTER_PLACEMENT<br>\n";
				echo $show["renewal_featured_ad"]." is RENEWAL_FEATURED_AD<br>\n";
				echo $show["renewal_featured_ad_2"]." is RENEWAL_FEATURED_AD_2<br>\n";
				echo $show["renewal_featured_ad_3"]." is RENEWAL_FEATURED_AD_3<br>\n";
				echo $show["renewal_featured_ad_4"]." is RENEWAL_FEATURED_AD_4<br>\n";
				echo $show["renewal_featured_ad_5"]." is RENEWAL_FEATURED_AD_5<br>\n";
				echo $show["renewal_attention_getter"]." is RENEWAL_ATTENTION_GETTER<br>\n";
				echo $show["renewal_attention_getter_choice"]." is RENEWAL_ATTENTION_GETTER_CHOICE<br>\n";

				echo $show["bolding_upgrade"]." is BOLDING_UPGRADE<br>\n";
				echo $show["better_placement_upgrade"]." is BETTER_PLACEMENT_UPGRADE<br>\n";
				echo $show["featured_ad_upgrade"]." is FEATURED_AD_UPGRADE<br>\n";
				echo $show["featured_ad_2_upgrade"]." is FEATURED_AD_2_UPGRADE<br>\n";
				echo $show["featured_ad_3_upgrade"]." is FEATURED_AD_3_UPGRADE<br>\n";
				echo $show["featured_ad_4_upgrade"]." is FEATURED_AD_4_UPGRADE<br>\n";
				echo $show["featured_ad_5_upgrade"]." is FEATURED_AD_5_UPGRADE<br>\n";
				echo $show["attention_getter_upgrade"]." is ATTENTION_GETTER_UPGRADE<br>\n";
				echo $show["attention_getter_choice_upgrade"]." is ATTENTION_GETTER_CHOICE_UPGRADE<br>\n";
			}

			$this->sql_query = "SELECT * FROM ".$this->user_groups_price_plans_table." where id = ".$show["seller"];
			$price_plan_id_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions)
			{
				echo "<br>\n".$show["seller"]." IS THE SELLER<BR>\n";
				echo $this->sql_query." is the query <br>\n";
			}

			if (!$price_plan_id_result)
			{
				echo "No price_plan_id_result";
				return false;
			}
			elseif ($price_plan_id_result->RecordCount() == 1)
			{
				$show_price_plan_id = $price_plan_id_result->FetchNextObject();
				$this->sql_query = "SELECT * FROM ".$this->price_plan_table." where price_plan_id = ".$show_price_plan_id->AUCTION_PRICE_PLAN_ID;
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$price_plan_result = $db->Execute($this->sql_query);
				if (!$price_plan_result)
				{
					return false;
				}
				elseif ($price_plan_result->RecordCount() == 1)
				{
					$price_plan = $price_plan_result->FetchNextObject();
					if (($price_plan->CHARGE_PERCENTAGE_AT_AUCTION_END)  && ($price_plan->ROLL_FINAL_FEE_INTO_FUTURE))
					{
						$this->sql_query = "SELECT * FROM ".$this->classifieds_table." where final_fee = 1 and final_fee_transaction_number = 0 and ends < ".time()." and seller = ".$show["seller"];
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						$final_fee_result = $db->Execute($this->sql_query);
						if (!$final_fee_result)
						{
							if ($this->debug_transactions) echo $this->sql_query."<br>\n";
							return false;
						}
						elseif ($final_fee_result->RecordCount() > 0)
						{
							while ($show_final_fee = $final_fee_result->FetchNExtObject())
							{
								//clear open final fee transactions
								$this->sql_query = "update ".$this->classifieds_table." set
									final_fee_transaction_number = 9999999999
									where id = ".$show_final_fee->ID;
								$update_final_fee_result = $db->Execute($this->sql_query);
								if ($this->debug_transactions) echo $this->sql_query."<br>\n";
								if (!$update_final_fee_result)
								{
									if ($this->debug_transactions) echo $this->sql_query."<br>\n";
									return false;
								}
							}
						}
					}
				}
				else
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					//echo "no price plan result<BR>\n";
				}
			}

			if ($show["renewal_payment_expected"] == 1)
			{
				//renewal
				if ($show["renewal_length"])
				{
					if ($show["ends"] > $this->shifted_time())
						$new_expire = ($show["ends"] + ($show["renewal_length"] * 86400));
					else
						$new_expire = ($this->shifted_time() + ($show["renewal_length"]  * 86400));
					$this->sql_query = "update ".$this->classifieds_table." set
						date = \"".$this->shifted_time()."\",
						ends = \"".$new_expire."\",
						bolding = 0,
						better_placement = 0,
						featured_ad = 0,
						attention_getter = 0,
						expiration_notice = 0,
						date = \"".$this->shifted_time()."\",
						live = 1
						where id = ".$transaction_id;
					$renew_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$renew_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_bolding_feature"]) && ($show["renewal_bolding"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						bolding = 1
						where id = ".$transaction_id;
					$bolding_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$bolding_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_better_placement_feature"]) && ($show["renewal_better_placement"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						better_placement = 1
						where id = ".$transaction_id;
					$better_placement_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$better_placement_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_featured_feature"]) && ($show["renewal_featured_ad"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_featured_feature"]) && ($show["renewal_featured_ad_2"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_2 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_featured_feature"]) && ($show["renewal_featured_ad_3"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_3 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_featured_feature"]) && ($show["renewal_featured_ad_4"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_4 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_featured_feature"]) && ($show["renewal_featured_ad_5"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_5 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_attention_getters"]) && ($show["renewal_attention_getter"])
					 && ($show["renewal_attention_getter_choice"]))
				{
					$this->sql_query = "select * from ".$this->choices_table ." where choice_id = ".$show["renewal_attention_getter_choice"];
					$attention_getter_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$attention_getter_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($attention_getter_result->RecordCount() == 1)
					{
						$show_attention_getter = $attention_getter_result->FetchRow();
						$attention_getter_url = $show_attention_getter["value"];
					}
					else
					{
						$site->renew_upgrade_variables["attention_getter"] = 0;
						$attention_getter_url = "";
					}

					$this->sql_query = "update ".$this->classifieds_table." set
						attention_getter = 1,
						attention_getter_url = \"".$attention_getter_url."\"
						where id = ".$transaction_id;
					$update_attention_getter_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$update_attention_getter_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				$this->sql_query = "update ".$this->classifieds_table." set
					renewal_payment_expected = 0,
					renewal_payment_expected_by = 0,
					renewal_length = 0,
					renewal_bolding = 0,
					renewal_better_placement = 0,
					renewal_featured_ad = 0,
					renewal_featured_ad_2 = 0,
					renewal_featured_ad_3 = 0,
					renewal_featured_ad_4 = 0,
					renewal_featured_ad_5 = 0,
					renewal_attention_getter = 0,
					renewal_attention_getter_choice = 0,
					featured_ad_upgrade = 0,
					featured_ad_2_upgrade = 0,
					featured_ad_3_upgrade = 0,
					featured_ad_4_upgrade = 0,
					featured_ad_5_upgrade = 0,
					bolding_upgrade = 0,
					better_placement_upgrade = 0,
					attention_getter_upgrade = 0,
					attention_getter_choice_upgrade = 0,
					live = 1
					where id = ".$transaction_id;
				$renew_result = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$renew_result)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				}
			}
			elseif ($show["renewal_payment_expected"] == 2)
			{
				//upgrade
				if (($this->configuration_data["use_bolding_feature"]) && ($show["bolding_upgrade"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						bolding = 1
						where id = ".$transaction_id;
					$bolding_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$bolding_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_better_placement_feature"]) && ($show["better_placement_upgrade"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						better_placement = 1
						where id = ".$transaction_id;
					$better_placement_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$better_placement_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_featured_feature"]) && ($show["featured_ad_upgrade"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_featured_feature"]) && ($show["featured_ad_2_upgrade"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_2 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}
				if (($this->configuration_data["use_featured_feature"]) && ($show["featured_ad_3_upgrade"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_3 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}
				if (($this->configuration_data["use_featured_feature"]) && ($show["featured_ad_4_upgrade"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_4 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}
				if (($this->configuration_data["use_featured_feature"]) && ($show["featured_ad_5_upgrade"]))
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						featured_ad_5 = 1
						where id = ".$transaction_id;
					$featured_ad_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$featured_ad_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}

				if (($this->configuration_data["use_attention_getters"]) && ($show["attention_getter_upgrade"])
					 && ($show["attention_getter_choice_upgrade"]))
				{
					$this->sql_query = "select * from ".$this->choices_table ." where choice_id = ".$show["attention_getter_choice_upgrade"];
					$attention_getter_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$attention_getter_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($attention_getter_result->RecordCount() == 1)
					{
						$show_attention_getter = $attention_getter_result->FetchRow();
						$attention_getter_url = $show_attention_getter["value"];
					}
					else
					{
						$site->renew_upgrade_variables["attention_getter"] = 0;
						$attention_getter_url = "";
					}

					$this->sql_query = "update ".$this->classifieds_table." set
						attention_getter = 1,
						attention_getter_url = \"".$attention_getter_url."\"
						where id = ".$transaction_id;
					$update_attention_getter_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$update_attention_getter_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
				}
				// Change end time
				if($this->configuration_data['upgrade_time'])
				{
					$end_time = ($show['ends'] - $show['date']) + $this->shifted_time();
					$this->sql_query = "update geodesic_classifieds set
							ends = ".$end_time."
							where id = ".$this->classified_id;
					$end_result = $db->Execute($this->sql_query);
				}
				$this->sql_query = "update ".$this->classifieds_table." set
					renewal_payment_expected = 0,
					renewal_payment_expected_by = 0,
					renewal_length = 0,
					renewal_bolding = 0,
					renewal_better_placement = 0,
					renewal_featured_ad = 0,
					renewal_featured_ad_2 = 0,
					renewal_featured_ad_3 = 0,
					renewal_featured_ad_4 = 0,
					renewal_featured_ad_5 = 0,
					renewal_attention_getter = 0,
					renewal_attention_getter_choice = 0,
					featured_ad_upgrade = 0,
					featured_ad_2_upgrade = 0,
					featured_ad_3_upgrade = 0,
					featured_ad_4_upgrade = 0,
					featured_ad_5_upgrade = 0,
					bolding_upgrade = 0,
					better_placement_upgrade = 0,
					attention_getter_upgrade = 0,
					attention_getter_choice_upgrade = 0,
					live = 1
					where id = ".$transaction_id;
				$renew_result = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$renew_result)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				}
			}
			else
			{
				if ($show["item_type"] == 1)
				{
					//when ad ends
					$length_of_ad = ($show["duration"] * 86400);
					$current_time = $this->shifted_time();
					$this->sql_query = "update ".$this->classifieds_table." set
						live = 1,
						date = ".$current_time.",
						ends = ".($current_time + $length_of_ad)."
						where id = ".$transaction_id;
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					$result = $db->Execute($this->sql_query);
					if (!$result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						exit;
					}

				}
				elseif ($show["item_type"] == 2)
				{
					if($show["end_time"] == 0)
					{
						if($show["start_time"] == 0)
						{
							$ends = $this->DateAdd("d",$current_time,$show["classified_length"]);
						}
						else
						{
							$ends = $this->DateAdd("d",$show["start_time"],$show["classified_length"]);
						}
					}
					else
					{
						$ends = $show["end_time"];
					}
					//sunit

					if($show["start_time"] == 0)
					{
						$listing_starts = $this->shifted_time($db);
					}
					else
					{
						$listing_starts = $show["start_time"];
					}

					$this->sql_query = "update ".$this->classifieds_table." set
						live = 1,
						date = ".$listing_starts.",
						ends =".$ends."
						where id = ".$transaction_id;
					$live_result = $db->Execute($this->sql_query);

					//Added by Sunit
					$this->sql_query = " select transaction_type from ".$this->classifieds_table." where id = ".$transaction_id;
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					$tran_result = $db->Execute($this->sql_query);
					if($this->configuration_data->DEBUG_ADMIN)
					{
						$this->debug_display($db, $this->filename, $this->function_name, "auctions_table", "find transaction_type of the auction");
					}
					if (!$tran_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						exit;
					}
					else
					{
						$show_transaction = $tran_result->FetchNextObject();
						if(($show_transaction->TRANSACTION_TYPE == 1) ||($show_transaction->TRANSACTION_TYPE == 4) ||($show_transaction->TRANSACTION_TYPE == 5))
						{
							$this->sql_query = " select seller from ".$this->classifieds_table." where id = ".$transaction_id;
							if ($this->debug_transactions) echo $this->sql_query."<br>\n";
							$seller_result = $db->Execute($this->sql_query);
							$show_seller = $seller_result->FetchNextObject();

							$this->sql_query = " update ".$this->classifieds_table." set final_fee_transaction_number = 9999999  WHERE final_fee =1 AND live =0 AND final_fee_transaction_number =0 AND ends <".time()." AND seller =".$show["seller"];
							if ($this->debug_transactions) echo $this->sql_query."<br>\n";
							$result = $db->Execute($this->sql_query);
							if($this->configuration_data->DEBUG_ADMIN)
							{
								$this->debug_display($db, $this->filename, $this->function_name, "auctions_table", "update final_fee_transaction_number if auction is of type cash, money order or cheque");
							}
							if (!$result)
							{
								if ($this->debug_transactions) echo $this->sql_query."<br>\n";
								exit;
							}
						}
					}
					//Added by Sunit
				}
				if ($this->debug_transactions)
				{
					echo "about to send successful placement email\n";
					echo "about to check filters\n";
				}
				$this->sell_success_email($db,$transaction_id);
				include("../classes/site_class.php");
				include("../classes/user_management_ad_filters.php");
				$user_management = new User_management_ad_filters($db,$language_id,$show["seller"]);
				$user_management->check_ad_filters($db,$transaction_id);

			}

			if ($this->debug_transactions) echo "check_subscriptions_and_credits and  update_category_count<br>\n";
			$this->check_subscriptions_and_credits($db,$transaction_id);
			$this->update_category_count($db,$show["category"]);

			if ($this->debug_transactions) echo "END OF APPROVE_TRANSACTION<Br>\n";
			return true;
		}
		else
		{
			if ($this->debug_transactions) echo "END OF APPROVE_TRANSACTION<Br>\n";
			return false;
		}
	} //end of function approve_transaction

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function view_user_transactions($db,$user_id=0,$type=1,$page=0)
	{
		$this->get_configuration_data($db);

		if ($this->configuration_data['positive_balances_only']==0 && $this->configuration_data['site_balance_override']==0)
		{
			$balance_invoice_use = "Invoice Use";
			$balance_invoice_pay = "Invoice Payment";
		}
		else
		{
			$balance_invoice_use = "Site Balance Use";
			$balance_invoice_pay = "Site Balance Deposit";
		}
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";

		// Set title and text for tooltip
		if ($balance_invoice == "Invoice Payment")
			$this->body .= "
				Text[1] = [\"$balance_invoice_use\", \"This was the form of payment used to pay for a transaction.  It was NOT a payment towards an invoice.  The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n,
				Text[2] = [\"$balance_invoice_pay\", \"Listed here are <b>payments towards an invoice</b> The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n";
		else
			$this->body .= "
				Text[1] = [\"$balance_invoice_use\", \"This was the form of payment used to pay for a transaction.  It was NOT a deposit of site balance funds.  The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n,
				Text[2] = [\"$balance_invoice_pay\", \"Listed here are <b>deposits of additional money</b> towards the user's site balance.  The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n";

		//".$this->show_tooltip(1,1)."
		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";
		$balance_invoice_use .= $this->show_tooltip(1,1);


		/*$this->sql_query = "
			SELECT * FROM
				".$this->classifieds_table." AS class,
				".$this->classifieds_expired_table." AS class_ex,
				".$this->balance_transactions." as balance
			WHERE
				class.seller = class_ex.seller
			OR
				class.seller = balance.user_id
			OR
				class.seller = ".$user_id;

		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}*/

		if ($user_id && $type)
		{
			if ($page)
			{
				if ($page > 1)
				{
					$limit = ((($page - 1) * 25) + 1);
					$limit_phrase = ($limit).",25 ";
					//$limit_phrase = $limit;
				}
				else
					$limit_phrase = " 0,25";
			}
			else
			{
				$page = 1;
				$limit_phrase = " 0,25";
			}

			switch ($type)
			{
				case 1://CURRENT LISTING PLACEMENT
					$sql_count_query = "SELECT COUNT(*) AS total_transactions FROM ".$this->classifieds_table." WHERE seller = ".$user_id;
					$this->sql_query = "SELECT * FROM ".$this->classifieds_table." WHERE seller = ".$user_id;
				break;
				case 2://PAST LISTING PLACEMENT
					$sql_count_query = "SELECT COUNT(*) AS total_transactions FROM ".$this->classifieds_expired_table." WHERE seller = ".$user_id;
					$this->sql_query = "SELECT * FROM ".$this->classifieds_expired_table." WHERE seller = ".$user_id;
				break;
				case 3://BALANCE PURCHASE/INVOICE PAYMENT
					$sql_count_query = "SELECT COUNT(*) AS total_transactions FROM ".$this->balance_transactions." WHERE user_id = ".$user_id;
					$this->sql_query = "SELECT * FROM ".$this->balance_transactions." WHERE user_id = ".$user_id;
				break;
				/*case 4://SUBSCRIPTION RENEWAL

				break;
				case 5://LISTING UPGRADE/RENEWAL

				break;
				default:*/
					return false;
				break;
			}
			$ads_count_result = $db->Execute($sql_count_query);
			if ($this->debug_transactions) echo $sql_count_query."<br>\n";
			if (!$ads_count_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			$show = $ads_count_result->FetchRow();
			$total_transactions = $show["total_transactions"];

			$this->sql_query .= " order by date asc limit ".$limit_phrase;
			$ads_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$ads_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}

			$user = $this->get_user_data($db,$user_id);
			if ($ads_result->RecordCount() > 0)
			{
				if ($user)
				{
					switch ($type)
					{
						case 1://CURRENT LISTING PLACEMENT
							$this->title = "Current Listing Placement Transactions for ".stripslashes($user["firstname"])."&nbsp;
								".stripslashes($user["lastname"])." (".$user["username"].")";
							$this->description = "Below are the	current transactions from live listings for this user.
								Click the view button to see that transaction details.";
						break;
						case 2://PAST LISTING PLACEMENT
							$this->title = "Past Listing Placement Transactions for ".stripslashes($user["firstname"])."&nbsp;
								".stripslashes($user["lastname"])." (".$user["username"].")";
							$this->description = "Below are the current transactions from expired listings for this user.
								Click the view button to see that transaction details.";
						break;
						case 3://BALANCE PURCHASE/INVOICE PAYMENT
							$this->title = $balance_invoice_pay." Transactions for ".stripslashes($user["firstname"]).
								"&nbsp;".stripslashes($user["lastname"])." (".$user["username"].")";
							if ($balance_invoice == "Invoice Payment")
								$this->description = "Below are the	$balance_invoice_pay transactions for this user.
									Click the view button to see the transaction details.  Each of these was a <b>payment towards
									an invoice</b>.  NOTE:  The information displayed may be inaccurate if you have recently changed
									your settings in 'Site Balance Setup'.";
							else
								$this->description = "Below are the	$balance_invoice_pay transactions for this user.
									Click the view button to see the transaction details.  Each of these was a <b>purchase of
									additional money</b> towards the user's site balance. NOTE:  The information displayed may
									be inaccurate if you have recently changed your settings in 'Site Balance Setup'.";
						break;
						/*case 4://SUBSCRIPTION RENEWAL
							$this->title = "Subscription Renewal Transactions for ".stripslashes($user["firstname"])."&nbsp;
								".stripslashes($user["lastname"])." (".$user["username"].")";
							$this->description = "Below are the	subscription renewal transactions for this user.
								Click the view button to see that transaction details.";
						break;
						case 5://LISTING UPGRADE/RENEWAL
							$this->title = "Listing Upgrade&nbsp;/&nbsp;Renewal Transactions for ".stripslashes($user["firstname"])."&nbsp;
								".stripslashes($user["lastname"])." (".$user["username"].")";
							$this->description = "Below are the	listing upgrade&nbsp;/&nbsp;renewal transactions for this user.
								Click the view button to see that transaction details.";
						break;*/
						default:
							return false;
						break;
					}

					$this->body .= "
							<table cellpadding=2 cellspacing=1 border=0 valign=top align=center class=row_color1 width=100%>";
					if ($total_transactions > 25)
					{
						$this->body .= "
								<tr class=row_color_black>
									<td colspan=4 class=medium_font_light>
										page results - ";
						//get total number of pages
						$number_of_page_results = ceil($total_transactions / 25);
						for ($i = 1;$i <= $number_of_page_results;$i++)
						{
							if ($page == $i)
							{
								$this->body .= "<span class=medium_font_light><b>".$i."</b></span> ";
							}
							else
							{
								$this->body .=  "<a href=index.php?a=41&b=".$user_id."&d=".$i."&e=".$type;
								$this->body .=  "><span class=medium_font_light>".$i."</span></a> ";
							}
						}
						$this->body .= "&nbsp;
									</td>
								</tr>";
					}
					$this->body .= "
								<tr class=row_color_black>
									<td class=medium_font_light width=40%>Date</td>
									<td class=medium_font_light>Form of Payment</td>
									<td class=medium_font_light>Amount</td>
									<td>&nbsp;</td>
								</tr>";
					$this->sql_query = "SELECT";
					$this->row_count = 0;
					while ($show = $ads_result->FetchRow())
					{
						if ($this->debug_transactions)
						{
							echo "<br>payment_type - ".$show["payment_type"];
							echo "<br>transaction_type - ".$show["transaction_type"]."<br>";
						}
						$current_transaction = ($type == 3) ? $this->get_transaction_type($db,$show["payment_type"]) : $this->get_transaction_type($db,$show["transaction_type"]);

						if ($current_transaction["name"] == "Site Balance")
							$current_transaction["name"] = $balance_invoice_use;
						if ($current_transaction["name"] == "") continue;

						$total = ($type == 3) ? $show["amount"] : $show["total"];
						$id = ($type == 3) ? $show["transaction_id"] : $show["id"];
						$this->body .= "
								<tr class=".$this->get_row_color().">
									<td class=medium_font>
										".date("M d, Y H:i:s", $show["date"])."
									</td>
									<td class=medium_font>
										".$current_transaction["name"]."
									</td>
									<td class=medium_font>
										".$configuration_data["precurrency"]."&nbsp;
										".sprintf("%01.2f",$total)."&nbsp;
										".$configuration_data["postcurrency"];
						if ($show["renewal_length"] > 0)
						{
							//check to see what type the renewal are
							//paypal
							$this->sql_query = "select * from ".$this->paypal_transaction_table." where renew = 1 and id = ".$show->ID;
							$renewal_result = $db->Execute($this->sql_query);
							if (!$renewal_result)
							{
								echo "error in retrieving paypal renewals<Br>\n";
								echo $db->ErrorMsg()."<br>\n";
							}
							elseif ($renewal_result->RecordCount() > 0)
							{
								while ($show_renewal = $renewal_result->FetchRow())
								{
									$this->body .= "<br>renewal amount: ".$show_renewal['payment_gross']." - ".$show_renewal['payment_date']."<BR>";
								}

							}

							//this needs to be completed for the following payment types
							//bital
							//manual
							//authorize.net
							//linkpoint
							//internet secure
							//payflow pro
						}

						$this->body.= "
									</td>
									<td>
										<a href=index.php?a=41&b=".$user_id."&c=".$id.">
											<span class=medium_font>
												<img src=admin_images/btn_admin_view.gif alt=view border=0>
											</span>
										</a>
									</td>
								</tr>";$this->row_count++;
					}
					if ($total_transactions > 25)
						$this->body .= $page_row;

					if ($type != 1)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=1>this user's <b>current listing placement</b> transactions</a>
								</td>
							</tr>";
					}
					if ($type != 2)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=2>this user's <b>past listing placement</b> transactions</a>
								</td>
							</tr>";
					}
					if ($type != 3)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id.
										"&e=3>this user's <b>$balance_invoice_pay</b> transactions
									</a>".$this->show_tooltip(2,1)."
								</td>
							</tr>";
					}
					/*if ($type != 4)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=4>this user's <b>subscription renewal</b> transactions</a>
								</td>
							</tr>";
					}
					if ($type != 5)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=5>this user's <b>listing upgrade&nbsp;/&nbsp;renewal</b> transactions</a>
								</td>
							</tr>";
					}*/
					$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=17&b=".$user_id."&e=1>this user's current info</a>
								</td>
							</tr>
						</table>";
					return true;
				}
				else
				{
					return false;
				}

			}
			else
			{
				if ($user)
				{
					$this->title = "Transactions for ".stripslashes($user["username"]);

					$this->body .= "<font class=medium_font><b>There are no transactions for this user.</b></font><Br><Br><br>";

					$this->body .= "
						<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>";
					if ($type == 1)
					{
						$this->title = "Current Transactions for ".stripslashes($user["firstname"])." ".stripslashes($user["lastname"])." (".$user["username"].")";
						$this->description = "<span class=medium_error_font>There are no current transactions for this user.</font>";
					}
					elseif ($type == 2)
					{
						$this->title = "Past Transactions for ".stripslashes($user["firstname"])." ".stripslashes($user["lastname"])." (".$user["username"].")";
						$this->description = "<span class=medium_error_font>There are no past transactions for this user.</font>";
					}
					if ($type != 1)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=1>this user's <b>current listing placement</b> transactions</a>
								</td>
							</tr>";
					}
					if ($type != 2)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=2>this user's <b>past listing placement</b> transactions</a>
								</td>
							</tr>";
					}
					if ($type != 3)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=3>
										this user's <b>$balance_invoice_pay</b> transactions
									</a>".$this->show_tooltip(2,1)."
								</td>
							</tr>";
					}
					/*if ($type != 4)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=4>this user's <b>subscription renewal</b> transactions</a>
								</td>
							</tr>";
					}
					if ($type != 5)
					{
						$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=41&b=".$user_id."&e=5>this user's <b>listing upgrade/renewal</b> transactions</a>
								</td>
							</tr>";
					}*/
					$this->body .= "
							<tr>
								<td class=medium_font>
									<a href=index.php?a=17&b=".$user_id."&e=1>this user's current info</a>
								</td>
							</tr>
						</table>";
					return true;
				}
				else
					return false;
			}
		}
		else
		{
			//echo "missing something<br>\n";
			return false;
		}

	} //end of function view_user_transactions

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function view_this_user_transaction($db,$transaction_id=0,$user_id=0,$return_info=0,$return_switch=0)
	{
		/*echo $user_id." is user_id<br>\n";
		echo $transaction_id." is transaction_id<br>\n";*/
		if ($user_id && $transaction_id)
		{
			$user = $this->get_user_data($db,$user_id);
			$this->sql_query = "select * from ".$this->classifieds_table." where seller = ".$user_id." and id = ".$transaction_id;
			$current_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo "<BR>QUERY - ".$this->sql_query."<br>\n";
			if (!$current_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($current_result->RecordCount() == 1)
			{
				$show_transaction = $current_result->FetchRow();
				$this->show_transaction_details($db,$show_transaction,$user);
				if ($return_info)
				{
					$this->body .= "<table cellpadding=2 cellspacing=1 border=1 align=center class=row_color1 width=100%>\n";
					$this->body .= "<tr>\n\t<td>";
					if ($return_switch == 1)
						$this->body .= "<a href=index.php?a=42&b[month]=".$return_info["month"]."&b[year]=".$return_info["year"]."&b[page]=".$return_info["page"]."&z=".$return_switch."><span class=medium_font>back to ".$return_info["month"]."/".$return_info["year"]."</span></a></td>\n</tr>\n";
					elseif ($return_switch == 2)
					{
						$this->body .= "<a href=index.php?a=42&z=2&b[beginning_year]=".date("Y",$return_info["beginning"])."&b[beginning_month]=".date("n",$return_info["beginning"])."&b[beginning_day]=".date("j",$return_info["beginning"])."&b[beginning_hour]=".date("H",$return_info["beginning"])."&b[beginning_minute]=".date("i",$return_info["beginning"])."&b[ending_year]=".date("Y",$return_info["ending"])."&b[ending_month]=".date("n",$return_info["ending"])."&b[ending_day]=".date("j",$return_info["ending"])."&b[ending_hour]=".date("H",$return_info["ending"])."&b[ending_minute]=".date("i",$return_info["ending"])."&b[page]=".$return_info["page"]."><span class=medium_font>back to Transactions from ".date("r",$beginning_time)." to ".date("r".$return_info["ending"])."</span></a></td>\n</tr>\n";
					}
					$this->body .= "</table>\n";
				}
				return true;
			}
			else
			{
				$this->sql_query = "select * from ".$this->classifieds_expired_table." where seller = ".$user_id." and id = ".$transaction_id;
				$expired_result = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$expired_result)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($expired_result->RecordCount() == 1)
				{
					$show_transaction = $current_result->FetchRow();
					$this->show_transaction_details($db,$show_transaction,$user);
					if ($return_info)
					{
						$this->body .= "
							<table cellpadding=2 cellspacing=1 border=1 align=center class=row_color1 width=100%>
								<tr>
									<td>";
						if ($z == 1)
							$this->body .= "<a href=index.php?a=42&b[month]=".$return_info["month"]."&b[year]=".$return_info["year"]."&b[page]=".$return_info["page"]."&z=".$z."><span class=medium_font>back to ".JDmonthname($return_info["month"],1).",".$return_info["year"]."</span></a></td>\n</tr>\n";
						else
							$this->body .= "<a href=index.php?a=42&b[month]=".$return_info["month"]."&b[year]=".$return_info["year"]."&b[page]=".$return_info["page"]."><span class=medium_font>back to ".JDmonthname($return_info["month"],1)."</span></a></td>\n</tr>\n";
						$this->body .= "
									</td>
								</tr>
							</table>\n";
					}
					return true;
				}
				else
				{
					//check balance transaction table
					$this->sql_query = "select * from ".$this->balance_transactions." where transaction_id = ".$transaction_id;
					$balance_transaction_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$balance_transaction_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						$this->site_error($db->ErrorMsg());
						return false;
					}
					elseif ($balance_transaction_result->RecordCount() == 1)
					{
						$show_transaction = $balance_transaction_result->FetchRow();
						$this->show_transaction_details($db,$show_transaction,$user,1);

						return true;
					}
					else
					{
						return false;
					}
				}
			}
		}
		else
		{
			return false;
		}
	} //end of function view_this_user_transaction

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_transaction_details($db,$transaction,$user,$balance_transaction=0)
	{
		$this->get_configuration_data($db);

		if ($this->configuration_data['positive_balances_only']==0 && $this->configuration_data['site_balance_override']==0)
		{
			$balance_invoice_use = "Invoice Use";
			$balance_invoice_pay = "Invoice Payment";
		}
		else
		{
			$balance_invoice_use = "Site Balance Use";
			$balance_invoice_pay = "Site Balance Deposit";
		}
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";

		// Set title and text for tooltip
		if ($balance_invoice == "Invoice Payment")
			$this->body .= "
				Text[1] = [\"$balance_invoice_use\", \"This was the form of payment used to pay for a transaction.  It was NOT a payment towards an invoice.  The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n,
				Text[2] = [\"$balance_invoice_pay\", \"Listed here are <b>payments towards an invoice</b> The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n";
		else
			$this->body .= "
				Text[1] = [\"$balance_invoice_use\", \"This was the form of payment used to pay for a transaction.  It was NOT a deposit of site balance funds.  The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n,
				Text[2] = [\"$balance_invoice_pay\", \"Listed here are <b>deposits of additional money</b> towards the user's site balance.  The information displayed may be inaccurate if you have recently changed your settings in 'Site Balance Setup'.\"]\n";

		//".$this->show_tooltip(1,1)."
		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";
		$balance_invoice_use .= $this->show_tooltip(1,1);

		$title = ($balance_transaction) ? "Balance Transaction Details" : "Transaction Details";
		$trans_id = ($balance_transaction) ? $transaction["transaction_id"] : $transaction["id"];
		$total = ($balance_transaction) ? $transaction["amount"] : $transaction["total"];
		$payment_type = ($balance_transaction) ? $transaction["payment_type"] : $transaction["transaction_type"];
		$current_type = $this->get_transaction_type($db,$payment_type);
		if ($current_type["name"] == "Site Balance")
			$current_type["name"] = $balance_invoice_use;

		if ($this->debug_transactions) echo "<br>trans type - ".$payment_type."<br>";
		$this->title = "<b>".stripslashes($user["firstname"])." ".stripslashes($user["lastname"])."</b> $title";
		$this->description = "Below are the	details for this transaction.";
		$this->row_count = 1;
		$this->body .= "
			<table cellpadding=2 cellspacing=1 border=0 align=center class=row_color1 width=100%>
				<tr>
					<td>
						<table cellpadding=2 cellspacing=1 border=0 width=100%>
							<tr class=".$this->get_row_color().">
								<td align=right width=30% class=medium_font>date:</td>
								<td width=70% class=medium_font>".date("M d, Y H:i:s", $transaction["date"])."</td>
							</tr>";$this->row_count++;$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right class=medium_font>form of payment:</td>
								<td class=medium_font>".$current_type["name"]."</td>
							</tr>";$this->row_count++;$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right class=medium_font>features purchased:</td>
								<td class=medium_font>listing".
									(($transaction["attention_getter"]) ? "<br>attention getter" : "").
									(($transaction["bolding"]) ? "<br>bolding" : "").
									(($transaction["better_placement"]) ? "<br>better placement" : "").
									(($transaction["featured_ad"]) ? "<br>featured listing 1" : "").
									(($transaction["featured_ad_2"]) ? "<br>featured listing 2" : "").
									(($transaction["featured_ad_3"]) ? "<br>featured listing 3" : "").
									(($transaction["featured_ad_4"]) ? "<br>featured listing 4" : "").
									(($transaction["featured_ad_5"]) ? "<br>featured listing 5" : "").
								"</td>
							</tr>";$this->row_count++;$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right class=medium_font>subtotal: </td>
								<td class=medium_font>".$configuration_data["precurrency"]." ".sprintf("%01.2f",$transaction["subtotal"])." ".$configuration_data["postcurrency"]."</td>
							</tr>";$this->row_count++;$this->body .= "
							<tr class=".$this->get_row_color().">\n\t<td align=right class=medium_font>tax:</td>
								<td class=medium_font>".$configuration_data["precurrency"]." ".sprintf("%01.2f",$transaction["tax"])." ".$configuration_data["postcurrency"]."</td>
							</tr>";$this->row_count++;$this->body .= "
							<tr class=".$this->get_row_color().">
								<td align=right class=medium_font>total:</td>
								<td class=medium_font>".$configuration_data["precurrency"]." ".sprintf("%01.2f",$total)." ".$configuration_data["postcurrency"]."</td>
							</tr>";$this->row_count++;
		//paypal and credit card transaction specifics
		switch ($payment_type)
		{
			case 3: //paypal
				$this->display_paypal_transaction_details($db,$trans_id);
			break;

			case 2: //credit card
			{
				if (!($transaction["cc_transaction_type"] > 0))
				{
					$this->sql_query = "SELECT cc_id FROM $this->cc_choices WHERE chosen_cc = 1";
					$cc_result = $db->Execute($this->sql_query);
					if ($cc_result->RecordCount() == 1) $chosen_cc = $cc_result->FetchRow();
					else
					{
						return false;
					}
					$cc_transaction_type = $chosen_cc["cc_id"];
				}
				else
				{
					$cc_transaction_type = $transaction["cc_transaction_type"];
				}
				switch ($cc_transaction_type)
				{
					case 1: $this->display_authorizenet_transaction_details($db,$trans_id,$balance_transaction); break;
					case 2: $this->display_2checkout_transaction_details($db,$trans_id,$balance_transaction); break;
					case 3: $this->display_bital_transaction_details($db,$trans_id,$balance_transaction); break;
					case 4: $this->display_linkpoint_transaction_details($db,$trans_id,$balance_transaction); break;
					case 5: $this->display_internetsecure_transaction_details($db,$trans_id,$balance_transaction); break;
					case 6: $this->display_payflow_pro_transaction_details($db,$trans_id,$balance_transaction); break;
					case 7: $this->display_cc_paypal_transaction_details($db,$trans_id,$balance_transaction); break;
					case 9: $this->display_cc_manual_transaction_details($db,$trans_id,$balance_transaction); break;
					default: $this->body .= "no transaction type";
				}
			}
			break;

		} //end of switch

		$this->body .= "<tr>\n\t<td class=medium_font colspan=100%>\n\t<a href=index.php?a=41&b=".$user["id"].">this user's <b>current listing placement</b> transactions</a></td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font colspan=100%>\n\t<a href=index.php?a=41&b=".$user["id"]."&e=2>this user's <b>past listing placement</b> transactions</a></td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font colspan=100%>\n\t<a href=index.php?a=41&b=".$user["id"]."&e=3>this user's <b>$balance_invoice_pay</b> transactions</a>".$this->show_tooltip(2,1)."</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font colspan=100%>\n\t<a href=index.php?a=17&b=".$user["id"].">this user's info</a></td>\n</tr>\n</table>\n";
		$this->body .= "</table>\n";
		return true;
	} //end of function show_transaction_details

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function search_by_date_box()
	{
		//search between dates
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=42&z=2 method=post>\n";
		$this->body .= "<table cellspacing=1 cellpadding=2 border=0 width=100%>\n";
		$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\t<b>choose a beginning date:</b><br>";
		$this->get_fine_date_select("b[beginning_year]","b[beginning_month]","b[beginning_day]",
			"b[beginning_hour]","b[beginning_minute]",0,0,0);
		$this->body .= "</td>\n\t";
		$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\t<b>choose an ending date:</b><br>";
		$this->get_fine_date_select("b[ending_year]","b[ending_month]","b[ending_day]",
			"b[ending_hour]","b[ending_minute]",0,0,0);
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td colspan=2 class=medium_font align=center>\n\t<input type=submit name=submit value=\"Search\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";

		//search last #
		//$this->last_quantity_dropdown($name,$quantity=0);

		//$this->get_category_dropdown($db,$name,$category_id=0,$no_main=0);
	} //end of function search_by_date_box

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function group_dropdown($db)
	{
		$this->sql_query = "select * from ".$this->classified_groups_table;
		$result = $db->Execute($this->sql_query);
		if ($this->debug_transactions) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			$this->body .= "<span class=small_font>by group</span><select name=b[search_group]>\n\t\t";
			$this->body .= "<option value=0>all groups</option>\n\t\t";
			while ($show = $result->FetchRow())
			{
				$this->body .= "<option value=".$show["group_id"];
				if ($this->search_group == $show["group_id"])
					$this->body .= " selected";
				$this->body .= ">".$show["name"]."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t";
		}
	} //end of function group_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_category_dropdown($db,$name,$category_id=0,$no_main=0)
	{
		if (!$no_main)
		{
			array_push($this->category_dropdown_name_array, "All Categories");
			array_push($this->category_dropdown_id_array,0);
		}
		$this->get_subcategories_for_dropdown($db,0,0);

		//build the select statement
		//array_reverse($this->category_dropdown_name_array);
		//array_reverse($this->category_dropdown_id_array);
		$this->body .= "<select name=".$name.">\n\t\t";
		foreach($this->category_dropdown_name_array as $key => $value)
		{
			$this->body .= "<option ";
			if ($this->category_dropdown_id_array[$key] == $category_id)
				$this->body .= "selected";
			$this->body .= " value=".$this->category_dropdown_id_array[$key].">".$this->category_dropdown_name_array[$key]."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t";

     		return true;

	} //end of function get_category_dropdown

//##################################################################################

	function last_quantity_dropdown($name,$quantity=0)
	{
		if ($quantity = 0)
			$quantity = 25;
		$this->body .= "<select name=".$name.">\n\t\t";
		for ($i=1;$i<101;$i++)
		{
			$this->body .= "<option";
			if ($i == $quantity)
				$this->body .= " selected";
			$this->body .= ">".$i."</option>\n\t\t";
		}
		$this->body .= "</select>\n\t";

	} //end of function last_quantity_dropdown

//##################################################################################

	function get_transaction_type($db,$transaction_type=0)
	{
		if ($transaction_type)
		{
			$this->sql_query = "select * from ".$this->classified_payment_choices_table." where type = ".$transaction_type;
			$transaction_type_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$transaction_type_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($transaction_type_result->RecordCount() == 1)
			{
				$show = $transaction_type_result->FetchRow();
				return $show;
			}
			return true;
		}
		else
		{
			return false;
		}


	} //end of function get_transaction_type

//##################################################################################

	function view_transactions_by_month_form($db,$type=0)
	{
		if (!$type)
			$type=1;
		$this->sql_query = "select date from ".$this->classifieds_table." order by date asc limit 1";
		$earliest_result = $db->Execute($this->sql_query);
		if ($this->debug_transactions) echo $this->sql_query."<br>\n";
		if (!$earliest_result)
		{
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($earliest_result->RecordCount() == 1)
		{
			$show_earliest = $earliest_result->FetchRow();
			$earliest_month = date("n",$show_earliest["date"]);
			$earliest_year = date("Y",$show_earliest["date"]);
		}
		elseif ($earliest_result->RecordCount() == 0)
		{
			$earliest_month = date("n");
			$earliest_year = date("Y");
		}
		else
		{
			return false;
		}

		$this->sql_query = "select date from ".$this->classifieds_table." order by date desc limit 1";
		$latest_result = $db->Execute($this->sql_query);
		if ($this->debug_transactions) echo $this->sql_query."<br>\n";
		if (!$latest_result)
		{
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($latest_result->RecordCount() == 1)
		{
			$show_latest = $latest_result->FetchRow();
			$latest_month = date("n",$show_latest["date"]);
			$latest_year = date("Y",$show_latest["date"]);
		}
		elseif ($latest_result->RecordCount() == 0)
		{
			$latest_month = date("n");
			$latest_year = date("Y");
		}
		else
		{
			return false;
		}

		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=42&z=".$type." method=post>\n";
		$this->body .= "<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
		$this->body .= "<tr>\n\t<td align=center>\n\t";
		if ($latest_year == $earliest_year)
		{
			//just do the month
			$this->body .= "choose month<select name=b[month]>\n\t\t";
			for ($i = $earliest_month; $i <= $latest_month;$i++)
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			$this->body .= "</select><input type=hidden name=b[year] value=".$latest_year."> for the year - ".$latest_year."\n\t\t";
		}
		else
		{
			$this->body .= "choose month<select name=b[month]>\n\t\t";
			for ($i = 1; $i <= 12;$i++)
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t\t ";
			$this->body .= "<select name=b[year]>\n\t\t";
			for ($i = $earliest_year; $i <= $latest_year;$i++)
			{
				$this->body .= "<option>".$i."</option>\n\t\t";
			}
			$this->body .= "</select>\n\t\t";
		}
		$this->body .= "</td>\n</tr>\n";
		$this->body .= "<tr>\n\t<td class=medium_font align=center>\n\t<input type=submit name=submit value=\"Search\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n</form>\n";
		return  true;
	} //end of function view_transactions_by_month_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_transactions_by($db,$date_info=0,$type=0)
	{
		$this->sql_query = "select * from ".$this->site_configuration_table;
		$result = $db->Execute($this->sql_query);
		if ($this->debug_transactions) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();
		}

		if (($date_info) && ($type))
		{
			if ($type == 1)
			{
				//just show a single month
				$beginning_time = mktime(0,0,0,$date_info["month"],1,$date_info["year"]);
				$ending_time = mktime(23,59,59,$date_info["month"],31,$date_info["year"]);
			}
			elseif ($type == 2)
			{
				//show between dates
				if (($date_info["beginning_time"] == 0) || ($date_info["ending_time"] == 0))
				{
					$beginning_time = mktime($date_info["beginning_hour"],$date_info["beginning_minute"],0,$date_info["beginning_month"],$date_info["beginning_day"],$date_info["beginning_year"]);
					$ending_time = mktime($date_info["ending_hour"],$date_info["ending_minute"],0,$date_info["ending_month"],$date_info["ending_day"],$date_info["ending_year"]);
				}
				else
				{
					$beginning_time = $date_info["beginning_time"];
					$ending_time = $date_info["ending_time"];
				}
			}
			if ($date_info["page"])
			{
				$limit_statement = (($date_info["page"] - 1) * 25).",25";
			}
			else
			{
				$limit_statement = "0,25";
				$date_info["page"] = 1;
			}

			$this->sql_query = "select count(id) as total_count from ".$this->classifieds_table." where date > ".$beginning_time." and date < ".$ending_time." and seller != 0";
			$count_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$count_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($count_result->RecordCount() == 1)
			{
				$show_count = $count_result->FetchRow();
				$total_transactions = $show_count["total_count"];

				$this->sql_query = "select * from ".$this->classifieds_table." where date > ".$beginning_time." and date < ".$ending_time." and seller != 0 order by date asc limit ".$limit_statement;
				$month_result = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$month_result)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($month_result->RecordCount() > 0)
				{
					$this->body .= "<table cellspacing=1 cellpadding=2 border=0 width=100% class=row_color1>\n";
					$this->body .= "<tr class=row_color_red>\n\t<td colspan=7 class=very_large_font_light>\n\t";
					if ($type == 1)
					{
						//just show a single month
						//$this->title = "Transactions for ".date("F",$beginning_time)."/".date("Y",$beginning_time);
						$this->title = "Transactions for ".date("F j, Y, g:i a",$beginning_time);
						if ($total_transactions > 25)
						{
							$page_row = "<tr class=row_color_black>\n<td colspan=7 class=medium_font_light>\n\tpage results - ";
							//get total number of pages
							$number_of_page_results = ceil($total_transactions / 25);
							for ($i = 1;$i <= $number_of_page_results;$i++)
							{
								if ($date_info["page"] == $i)
								{
									$page_row .= "<span class=medium_font_light><b>".$i."</b></span> ";
								}
								else
								{
									$page_row .=  "<a href=index.php?a=42&z=1&";
									if ($date_info["month"])
										$page_row .=  "&b[month]=".$date_info["month"];
									if ($date_info["year"])
										$page_row .=  "&b[year]=".$date_info["year"];
									$page_row .=  "&b[page]=".$i;
									$page_row .=  "><span class=medium_font_light>".$i."</span></a> ";
								}
							}
							$page_row .=  "</td>\n</tr>\n";
							$this->body .= $page_row;
						}
					}
					elseif ($type == 2)
					{
						//show between dates
						//$this->title = "Transactions from ".date("r",$beginning_time)." to ".date("r".$ending_time);
						$this->title = "Transactions from ".date("F j, Y, g:i a",$beginning_time)." to ".date("F j, Y, g:i a",$ending_time);
						if ($total_transactions > 25)
						{
							$page_row = "<tr class=row_color_black>\n<td colspan=6 class=medium_font_light>\n\tpage results - ";
							//get total number of pages
							$number_of_page_results = ceil($total_transactions / 25);
							for ($i = 1;$i <= $number_of_page_results;$i++)
							{
								if ($date_info["page"] == $i)
								{
									$page_row .= "<span class=medium_font_light><u><b>".$i."</b></u></span> ";
								}
								else
								{
									$page_row .=  "<a href=index.php?a=42&z=2&";
									$page_row .=  "&b[beginning_time]=".$beginning_time;
									$page_row .=  "&b[ending_time]=".$ending_time;
									$page_row .=  "&b[page]=".$i;
									$page_row .=  "><span class=medium_font_light>".$i."</span></a> ";
								}
							}
							$page_row .=  "</td>\n</tr>\n";
							$this->body .= $page_row;
						}
					}
					$this->body .= " \n\t</td>\n</tr>\n";

					$this->body .= "<tr class=row_color_black>\n\t";
					$this->body .= "<td class=medium_font_light><b>Date</b> </td>\n\t";
					$this->body .= "<td class=medium_font_light><b>Username</b> </td>\n\t";
					if($this->is_class_auctions())
						$this->body .= "<td class=medium_font_light><b>Type</b></td>\n\t";
					$this->body .= "<td class=medium_font_light><b>Title</b> </td>\n\t";
					$this->body .= "<td class=medium_font_light><b>Trans type</b> </td>\n\t";
					$this->body .= "<td class=medium_font_light><b>Total</b> </td>\n\t";
					$this->body .= "<td>&nbsp;</td>\n</tr>\n";
					$this->row_count = 0;
					while ($show_transaction = $month_result->FetchRow())
					{
						$user_data = $this->get_user_data($db,$show_transaction["seller"]);
						$this->body .= "<tr class=".$this->get_row_color().">\n\t";
						$this->body .= "<td class=medium_font>".date("M d, Y H:i:s", $show_transaction["date"])." </td>\n\t";
						$this->body .= "<td class=medium_font>".$user_data["username"]." </td>\n\t";
						if($this->is_class_auctions())
						{
							if($show_transaction['item_type'] == 1)
								$this->body .= "<td class=medium_font>Classified</td>\n\t";
							elseif($show_transaction['item_type'] == 2)
								$this->body .= "<td class=medium_font>Auction</td>\n\t";
						}
						$this->body .= "<td class=medium_font>".stripslashes(urldecode($show_transaction["title"]))." </td>\n\t";
						$current_transaction = $this->get_transaction_type($db,$show_transaction["transaction_type"]);
						$this->body .= "<td class=medium_font>".$current_transaction["name"]." </td>\n\t";
						$this->body .= "<td class=medium_font>".$show_configuration["precurrency"]." ".sprintf("%01.2f",$show_transaction["total"])." ".$show_configuration["postcurrency"]." </td>\n\t";
						if ($type == 1)
							$this->body .= "<td align=center><a href=index.php?a=41&z=1&c=".$show_transaction["id"]."&d[month]=".$date_info["month"]."&d[year]=".$date_info["year"]."&d[page]=".$date_info["page"]."&b=".$show_transaction["seller"]."><span class=medium_font><img src=admin_images/btn_admin_view.gif alt=view border=0></span></a></td>\n</tr>\n";
						else
							$this->body .= "<td align=center><a href=index.php?a=41&z=2&c=".$show_transaction["id"]."&d[beginning]=".$beginning_time."&d[ending]=".$ending_time."&d[page]=".$date_info["page"]."&b=".$show_transaction["seller"]."><span class=medium_font><img src=admin_images/btn_admin_view.gif alt=view border=0></span></a></td>\n</tr>\n";
						$this->row_count++;
					}

					if ($total_transactions > 25)
					{
						$this->body .= $page_row;
					}

					$this->body .= "<tr>\n\t<td colspan=7 align=center><a href=index.php?a=40>
						<span class=medium_font><b>New Search</b></span></a>\n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";
					return true;
				}
				else
				{
					//no results for this month
		            $this->title = "Transactions > Search Transactions";
					$this->body .= "<table cellspacing=1 cellpadding=2 border=0 width=100%>\n";
					$this->body .= "<tr>\n<td align=center><span class=medium_font>
						<b><br><br>There are no transactions for the time period you entered.<br><br></b></span>\n\t</td>\n</tr>\n";
					if ($type == 1)
						$date_info["month"]."/".$date_info["year"];
					elseif ($type == 2)
					{
						$this->body .= "<span class=medium_font>month/day/year - hour:minute<br>from -- ".
						$date_info["beginning_month"]."/".$date_info["beginning_day"]."/".$date_info["beginning_year"]." - ".$date_info["beginning_hour"].":".$date_info["beginning_minute"];
						$this->body .= "<br>to -- ".
							$date_info["ending_month"]."/".$date_info["ending_day"]."/".$date_info["ending_year"]." - ".$date_info["ending_hour"].":".$date_info["ending_minute"];
					}
					$this->body .= " <br></span>";
					$this->body .= "<a href=index.php?a=40><span class=medium_font>start new search</span></a> \n\t</td>\n</tr>\n";
					$this->body .= "</table>\n";

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

	} //end of function display_transactions_by_month

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function sell_success_email($db,$classified_id=0)
	{
		if (($classified_id) && ($this->configuration_data["send_successful_placement_email"]))
		{
			$this->page_id = 51;
			$this->get_text($db);
			$ad_data = $this->get_classified_data($db,$classified_id);
			$user_data = $this->get_user_data($db,$ad_data["seller"]);
			if (($ad_data) && ($user_data))
			{

				$subject = urldecode($this->messages[712]);
				$message = urldecode($this->messages[713])." ".stripslashes($user_data["firstname"]).",\n";
				$message .= urldecode($this->messages[714])."\n\n";
				if ($ad_data["article_type"] == 2)
					$message .= $this->configuration_data["classifieds_url"]."?a=2&b=".$classified_id;
				else
					$message .= $this->configuration_data["classifieds_url"]."?a=2&b=".$classified_id;

				$from = "From: ".$this->configuration_data["site_email"];

				$additional = "-f".$this->configuration_data["site_email"];

				if ($this->configuration_data["email_configuration"] == 1)
					mail($user_data["email"],$subject,$message,$from,$additional);
				elseif ($this->configuration_data["email_configuration"] == 2)
					mail($user_data["email"],$subject,$message,$from);
				else
					mail($user_data["email"],$subject,$message);
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

	} //end of function sell_success_email

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function approve_subscription_renewal($db,$transaction_id=0)
	{
		if ($transaction_id)
		{
			$this->get_configuration_data($db);
			$this->sql_query = "select * from ".$this->classified_subscription_holds_table." where renewal_id = ".$transaction_id;
			$ad_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$ad_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				exit;
			}
			$show = $ad_result->FetchRow();

			//STOREFRONT CODE
			if($show["storefront"]==1)
			{
				include_once('storefront/admin_store.php');
				$this->sql_query = "select * from ".Admin_store::get("storefront_subscriptions_choices_table")."
					where period_id = ".$show["subscription_choice"]."";
				$choices_result = $db->Execute($this->sql_query);
				if (!$choices_result)
				{
					if ($this->debug_transactions) echo $this->sql_query." is the query <br>\n";
					return false;
				}
				elseif ($choices_result->RecordCount() == 1 )
				{
					$show_choice = $choices_result->FetchNextObject();
					if ($show_choice->VALUE !=0)
					{
						//check to see if currently subscribed
						$this->sql_query = "select * from ".Admin_store::get("storefront_subscriptions_table")." where user_id = ".$show["user_id"];
						$check_subscriptions_results = $db->Execute($this->sql_query);
						if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
						if (!$check_subscriptions_results)
						{
							if ($this->debug_transactions) echo $this->sql_query." is the query <br>\n";
							return false;
						}
						elseif ($check_subscriptions_results->RecordCount() > 0)
						{
							//extend subscription period
							$show_subscription = $check_subscriptions_results->FetchNextObject();
							if ($show_subscription->EXPIRATION > $this->shifted_time())
								$new_expire = ($show_subscription->EXPIRATION + ($show_choice->VALUE * 86400));
							else
								$new_expire = ($this->shifted_time() + ($show_choice->VALUE * 86400));
							$this->sql_query = "update ".Admin_store::get("storefront_subscriptions_table")."
								set expiration = ".$new_expire."
								where subscription_id = ".$show_subscription->SUBSCRIPTION_ID;
							$update_subscriptions_results = $db->Execute($this->sql_query);
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							if (!$update_subscriptions_results)
							{
								if ($this->debug_transactions) echo $this->sql_query." is the query <br>\n";
								return false;
							}

						}
						else
						{
							//enter new subscription period
							$new_expire = ($this->shifted_time() + ($show_choice->VALUE * 86400));
							$this->sql_query = "insert into ".Admin_store::get("storefront_subscriptions_table")."
								(user_id,expiration)
								values
								(".$show["user_id"].",".$new_expire.")";
							$insert_subscriptions_results = $db->Execute($this->sql_query);
							if ($this->debug_subscription) echo $this->sql_query." is the query <br>\n";
							if (!$insert_subscriptions_results)
							{
								if ($this->debug_transactions) echo $this->sql_query." is the query <br>\n";
								return false;
							}
						}
					}
				}
				//remove on hold
				$this->sql_query = "delete from ".$this->classified_subscription_holds_table." where renewal_id = ".$transaction_id;
				$delete_result = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$delete_result)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					exit;
				}
				return true;
			}
			//STOREFRONT CODE

			if (($show["subscription_choice"]) && ($show["user_id"]))
			{
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$show["user_id"];
				$price_plan_id_result = $db->Execute($this->sql_query);
				if ($price_plan_id_result->RecordCount() == 1)
				{
					$show_price_plan = $price_plan_id_result->FetchRow();
					$this->sql_query = "select * from ".$this->classified_subscription_choices_table." where price_plan_id = ".$show_price_plan["price_plan_id"]."
						and period_id = ".$show["subscription_choice"]." order by value asc";
					$choices_result = $db->Execute($this->sql_query);
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					if (!$choices_result)
					{
						if ($this->debug_transactions) echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($choices_result->RecordCount() == 1 )
					{
						$show_choice = $choices_result->FetchRow();
						if ($show_choice["value"] !=0)
						{
							//check to see if currently subscribed
							$this->sql_query = "select * from ".$this->classified_user_subscriptions_table." where user_id = ".$show["user_id"];
							if ($this->debug_transactions) echo $this->sql_query."<br>\n";
							$check_subscriptions_results = $db->Execute($this->sql_query);
							if (!$check_subscriptions_results)
							{
								if ($this->debug_transactions) echo $this->sql_query."<br>\n";
								return false;
							}
							elseif ($check_subscriptions_results->RecordCount() > 0)
							{
								//extend subscription period
								$show_subscription = $check_subscriptions_results->FetchRow();
								if ($show_subscription["subscription_expire"] > $this->shifted_time())
									$new_expire = ($show_subscription["subscription_expire"] + ($show_choice["value"] * 86400));
								else
									$new_expire = ($this->shifted_time() + ($show_choice["value"] * 86400));
								$this->sql_query = "update ".$this->classified_user_subscriptions_table."
									set subscription_expire = ".$new_expire."
									where subscription_id = ".$show_subscription["subscription_id"];
								$update_subscriptions_results = $db->Execute($this->sql_query);
								if ($this->debug_transactions) echo $this->sql_query."<br>\n";
								if (!$update_subscriptions_results)
								{
									if ($this->debug_transactions) echo $this->sql_query."<br>\n";
									return false;
								}

							}
							else
							{
								//enter new subscription period
								$new_expire = ($this->shifted_time() + ($show_choice["value"] * 86400));
								$this->sql_query = "insert into ".$this->classified_user_subscriptions_table."
									(user_id,subscription_expire)
									values
									(".$show["user_id"].",".$new_expire.")";
								$insert_subscriptions_results = $db->Execute($this->sql_query);
								if ($this->debug_transactions) echo $this->sql_query."<br>\n";
								if (!$insert_subscriptions_results)
								{
									if ($this->debug_transactions) echo $this->sql_query."<br>\n";
									return false;
								}
							}

							//remove on hold
							$this->sql_query = "delete from ".$this->classified_subscription_holds_table." where renewal_id = ".$transaction_id;
							$delete_result = $db->Execute($this->sql_query);
							if ($this->debug_transactions) echo $this->sql_query."<br>\n";
							if (!$delete_result)
							{
								if ($this->debug_transactions) echo $this->sql_query."<br>\n";
								exit;
							}
							return true;
						}
						else
							return false;
					}
					else
						return false;
				}
				else
					return false;
			}
			else
				return false;
		}
		else
		{
			return false;
		}
	} //end of function approve_subscription_renewal

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

 	function delete_subscription_renewal ($db,$transaction_id=0)
 	{
		if ($transaction_id)
		{
			$this->sql_query = "delete from ".$this->classified_subscription_holds_table." where renewal_id = ".$transaction_id;
			$delete_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$delete_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				exit;
			}
			return true;
		}
		else
			return false;
 	} // end of function delete_subscription_renewal

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

 	function delete_renewal ($db,$transaction_id=0)
 	{
		if ($transaction_id)
		{
			$this->sql_query = "update ".$this->classifieds_table." set
				renewal_payment_expected = 0,
				renewal_payment_expected_by = 0,
				renewal_length = 0,
				renewal_bolding = 0,
				renewal_better_placement = 0,
				renewal_featured_ad = 0,
				renewal_featured_ad_2 = 0,
				renewal_featured_ad_3 = 0,
				renewal_featured_ad_4 = 0,
				renewal_featured_ad_5 = 0,
				renewal_attention_getter = 0,
				renewal_attention_getter_choice = 0,
				featured_ad_upgrade = 0,
				featured_ad_2_upgrade = 0,
				featured_ad_3_upgrade = 0,
				featured_ad_4_upgrade = 0,
				featured_ad_5_upgrade = 0,
				bolding_upgrade = 0,
				better_placement_upgrade = 0,
				attention_getter_upgrade = 0,
				attention_getter_choice_upgrade = 0
				where id = ".$transaction_id;
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			$reset_renew_result = $db->Execute($this->sql_query);
			if (!$reset_renew_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				exit;
			}
			return true;
		}
		else
			return false;
	} // end of function delete_renewal

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function approve_balance_transaction($db,$transaction_id=0)
	{
		if ($transaction_id)
		{
			//get balance transaction data
			$this->sql_query = "select * from ".$this->balance_transactions." where transaction_id = ".$transaction_id;
			$balance_transaction_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$balance_transaction_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				return false;
			}
			elseif ($balance_transaction_result->RecordCount() ==1)
			{
				$balance_transaction = $balance_transaction_result->FetchRow();
				//get user data
				$user_data = $this->get_user_data($db,$balance_transaction["user_id"]);

				$new_balance = $user_data["account_balance"] + $balance_transaction["amount"];
				$this->sql_query = "update ".$this->userdata_table." set
					account_balance = ".$new_balance."
					where id = ".$balance_transaction["user_id"];
				$update_balance_results = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$update_balance_results)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					return false;
				}

				$this->sql_query = "update ".$this->balance_transactions." set approved = 1 where transaction_id = ".$transaction_id;
				$update_balance_transaction_result = $db->Execute($this->sql_query);
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				if (!$update_balance_transaction_result)
				{
					if ($this->debug_transactions) echo $this->sql_query."<br>\n";
					return false;
				}
			}
			else
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function approve_balance_transaction

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_balance_transaction($db,$transaction_id=0)
	{
		if ($transaction_id)
		{
			$this->sql_query = "delete from ".$this->balance_transactions." where transaction_id = ".$transaction_id;
			$remove_balance_transaction_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$remove_balance_transaction_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				return false;
			}
			else
			{
				return true;
			}
		}
		else
			return false;
	} //end of function delete_balance_transaction

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_paypal_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "id";
			$this->sql_query = "select * from ".$this->paypal_transaction_table." where $key = ".$transaction_id;
			$paypal_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$paypal_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($paypal_result->RecordCount() == 1)
			{
				$paypal = $paypal_result->FetchRow();
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>PayPal</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>receiver email:</td>
									<td align=left class=medium_font>".$paypal["receiver_email"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>item quantity:</td>
									<td align=left class=medium_font>".$paypal["quantity"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>item number:</td>
									<td align=left class=medium_font>".$paypal["item_number"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>number of cart items:</td>
									<td align=left class=medium_font>".$paypal["num_cart_items"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>\n\tpayer email:</td>
									<td align=left class=medium_font>".$paypal["payer_email"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>custom verification field:</td>
									<td align=left class=medium_font>".$paypal["custom"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>payment status:</td>
									<td align=left class=medium_font>".$paypal["payment_status"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>payment date:</td>
									<td align=left class=medium_font>".$paypal["payment_date"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>payment gross:</td>
									<td align=left class=medium_font>".$paypal["payment_gross"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>payment fee:</td>
									<td align=left class=medium_font>".$paypal["payment_fee"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>txn id:</td>
									<td align=left class=medium_font>".$paypal["txn_id"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>txn type:</td>
									<td align=left class=medium_font>".$paypal["txn_type"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>payer status:</td>
									<td align=left class=medium_font>".$paypal["payer_status"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>payment type:</td>
									<td align=left class=medium_font>".$paypal["payment_type"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>notify version:</td>
									<td align=left class=medium_font>".$paypal["notify_version"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>verify sign:</td>
									<td align=left class=medium_font>".$paypal["verify_sign"]."</td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
			else
			{
				return false;
			}
			return true;
		}
		else
			return false;

	} // end of function display_paypal_transaction_details

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_authorizenet_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "classified_id";
			$this->sql_query = "select * from  geodesic_cc_authorizenet_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$cc_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() == 1)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>Authorize.net</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>username: </td>
									<td class=medium_font>".$user["username"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>first name: </td>
									<td class=medium_font>".$cc["first_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>last name: </td>
									<td class=medium_font>".$cc["last_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>address: </td>
									<td class=medium_font>".$cc["address"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>city: </td>
									<td class=medium_font>".$cc["city"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>state: </td>
									<td class=medium_font>".$cc["state"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>country: </td>
									<td class=medium_font>".$cc["country"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>zip: </td>
									<td class=medium_font>".$cc["zip"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>email: </td>
									<td class=medium_font>".$cc["email"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>company: </td>
									<td class=medium_font>".$cc["company"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>fax: </td>
									<td class=medium_font>".$cc["fax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>description: </td>
									<td class=medium_font>".$cc["description"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>response code: </td>
									<td class=medium_font>".$cc["response_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>response sub code: </td>
									<td class=medium_font>".$cc["response_sub_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>response reason text: </td>
									<td class=medium_font>".$cc["response_reason_text"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>authorization code: </td>
									<td class=medium_font>".$cc["auth_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>avs code: </td>
									<td class=medium_font>".$cc["avs_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>transaction id: </td>
									<td class=medium_font>".$cc["trans_id"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>md5 verification hash: </td>
									<td class=medium_font>".$pacc["md5_hash"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>card number: </td>
									<td class=medium_font>".$cc["card_num"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>expiration date: </td>
									<td class=medium_font>".$cc["exp_date"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>transaction tax: </td>
									<td class=medium_font>".$cc["tax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font_light>transaction amount: </td>
									<td class=medium_font>".$cc["amount"]." </td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>.
					</tr>";
			}
			else
				return false;
			return true;
		}
		else
			return false;
	} // end of function display_authorizenet_transaction_details

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_2checkout_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "product_id";
			$this->sql_query = "select * from  geodesic_cc_twocheckout_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$cc_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() == 1)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>2Chechout</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Listing id: </td>
									<td align=left class=medium_font>".$user["product_id"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>username: </td>
									<td align=left class=medium_font>".$user["username"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>card holder name: </td>
									<td align=left class=medium_font>".$cc["card_holder_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>street address: </td>
									<td align=left class=medium_font>".$cc["street_address"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>city: </td>
									<td align=left class=medium_font>".$cc["city"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>state: </td>
									<td align=left class=medium_font>".$cc["state"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>country: </td>
									<td align=left class=medium_font>".$cc["country"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>zip: </td>
									<td align=left class=medium_font>".$cc["zip"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>email: </td>
									<td align=left class=medium_font>".$cc["email"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>product description: </td>
									<td align=left class=medium_font>".$cc["product_description"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>order number: </td>
									<td align=left class=medium_font>".$cc["order_number"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>merchant order id: </td>
									<td align=left class=medium_font>".$cc["merchant_order_id"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>transaction id: </td>
									<td align=left class=medium_font>".$cc["transaction_id"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>transaction tax: </td>
									<td align=left class=medium_font>".$cc["tax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>transaction amount: </td>
									<td align=left class=medium_font>".$cc["total"]." </td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
			else
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_bital_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "classified_id";
			$this->sql_query = "select * from  geodesic_cc_bitel_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$cc_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() == 1)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>Bitel</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>classified id: </td>
									<td align=left class=medium_font>".$user["classified_id"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>username: </td>
									<td align=left class=medium_font>".$user["username"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>first name: </td>
									<td align=left class=medium_font>".$cc["first_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>last name: </td>
									<td align=left class=medium_font>".$cc["last_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>address: </td>
									<td align=left class=medium_font>".$cc["address"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>city: </td>
									<td align=left class=medium_font>".$cc["city"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>state: </td>
									<td align=left class=medium_font>".$cc["state"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>country: </td>
									<td align=left class=medium_font>".$cc["country"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>zip: </td>
									<td align=left class=medium_font>".$cc["zip"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>email: </td>
									<td align=left class=medium_font>".$cc["email"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>company: </td>
									<td align=left class=medium_font>".$cc["company"]." </td
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>fax: </td>
									<td align=left class=medium_font>".$cc["fax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>description: </td>
									<td align=left class=medium_font>".$cc["description"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>response code: </td>
									<td align=left class=medium_font>".$cc["response_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>response error: </td>
									<td align=left class=medium_font>".$cc["response_error"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>authorization number: </td>
									<td align=left class=medium_font>".$cc["authorization_number"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>bitel transaction id: </td>
									<td align=left class=medium_font>".$cc["bitel_transaction_id"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>card number: </td>
									<td align=left class=medium_font>".$cc["card_num"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>expiration date: </td>
									<td align=left class=medium_font>".$cc["exp_date"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction tax: </td>
									<td align=left class=medium_font>".$cc["tax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction amount: </td>
									<td align=left class=medium_font>".$cc["amount"]." </td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
			else
				return false;
			return true;
		}
		else
			return false;
	} //end of function display_bital_transaction_details

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_linkpoint_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "classified_id";
			$this->sql_query = "select * from geodesic_cc_linkpoint_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$cc_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() == 1)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>Linkpoint</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>username: </td>
									<td align=left class=medium_font>".$user["username"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>first name: </td>
									<td align=left class=medium_font>".$cc["first_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>last name: </td>
									<td align=left class=medium_font>".$cc["last_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>address: </td>
									<td align=left class=medium_font>".$cc["address"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>city: </td>
									<td align=left class=medium_font>".$cc["city"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>state: </td>
									<td align=left class=medium_font>".$cc["state"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>country: </td>
									<td align=left class=medium_font>".$cc["country"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>zip: </td>
									<td align=left class=medium_font>".$cc["zip"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>email: </td>
									<td align=left class=medium_font>".$cc["email"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>company: </td>
									<td align=left class=medium_font>".$cc["company"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>fax: </td>
									<td align=left class=medium_font>".$cc["fax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>description: </td>
									<td align=left class=medium_font>".$cc["description"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>response code: </td>
									<td align=left class=medium_font>".$cc["r_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>authorization response: </td>
									<td align=left class=medium_font>".$cc["r_authresponse"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>authorization error: </td>
									<td align=left class=medium_font>".$cc["r_error"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>order num: </td>
									<td align=left class=medium_font>".$cc["r_ordernum"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>avs code: </td>
									<td align=left class=medium_font>".$cc["r_avs"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>card number: </td>
									<td align=left class=medium_font>".$cc["card_num"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>expiration date: </td>
									<td align=left class=medium_font>".$cc["exp_date"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction tax: </td>
									<td align=left class=medium_font>".$cc["tax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=row_color>
									<td width=30% align=right class=medium_font>transaction amount: </td>
									<td align=left class=medium_font>".$cc["amount"]." </td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
		}
		else
			return false;
	} //end of function display_linkpoint_transaction_details

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_internetsecure_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "classified_id";
			$this->sql_query = "select * from  geodesic_cc_internetsecure_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			if ($this->debug_transactions) echo $this->sql_query."<br>\n";
			if (!$cc_result)
			{
				if ($this->debug_transactions) echo $this->sql_query."<br>\n";
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() == 1)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr class=".$this->get_row_color().">
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>Internetsecure</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>username: </td>
									<td align=left class=medium_font>".$user["username"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>first name: </td>
									<td align=left class=medium_font>".$cc["first_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>last name: </td>
									<td align=left class=medium_font>".$cc["last_name"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>address: </td>
									<td align=left class=medium_font>".$cc["address"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>city: </td>
									<td align=left class=medium_font>".$cc["city"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>state: </td>
									<td align=left class=medium_font>".$cc["state"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>country: </td>
									<td align=left class=medium_font>".$cc["country"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>zip: </td>
									<td align=left class=medium_font>".$cc["zip"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>email: </td>
									<td align=left class=medium_font>".$cc["email"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>company: </td>
									<td align=left class=medium_font>".$cc["company"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>fax: </td>
									<td align=left class=medium_font>".$cc["fax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>description: </td>
									<td align=left class=medium_font>".$cc["description"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>response code: </td>
									<td align=left class=medium_font>".$cc["response_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>response sub code: </td>
									<td align=left class=medium_font>".$cc["response_sub_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>response reason text: </td>
									<td align=left class=medium_font>".$cc["response_reason_text"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>authorization code: </td>
									<td align=left class=medium_font>".$cc["auth_code"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>approval code: </td>
									<td align=left class=medium_font>".$cc["approvalcode"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction tax: </td>
									<td align=left class=medium_font>".$cc["tax"]." </td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction amount: </td>
									<td align=left class=medium_font>".$cc["amount"]." </td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
			else
				return false;
			return true;
		}
		else
			return false;
	} //end of function display_internetsecure_transaction_details

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_payflow_pro_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "classified_id";
			$this->sql_query = "select * from  geodesic_cc_payflow_pro_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$cc_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() == 1)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>Verisign Payflow Pro</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right align=right class=medium_font>username:</td>
									<td align=left class=medium_font>".$user["username"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>first name:</td>
									<td align=left class=medium_font>".$cc["first_name"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>last name:</td>
									<td align=left class=medium_font>".$cc["last_name"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>address:</td>
									<td align=left class=medium_font>".$cc["address"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>city:</td>
									<td align=left class=medium_font>".$cc["city"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>state:</td>
									<td align=left class=medium_font>".$cc["state"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>country:</td>
									<td align=left class=medium_font>".$cc["country"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>zip:</td>
									<td align=left class=medium_font>".$cc["zip"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>email:</td>
									<td align=left class=medium_font>".$cc["email"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>company:</td>
									<td align=left class=medium_font>".$cc["company"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>fax:</td>
									<td align=left class=medium_font>".$cc["fax"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>description:</td>
									<td align=left class=medium_font>".$cc["description"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>pnref:</td>
									<td align=left class=medium_font>".$cc["pnref"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>result:</td>
									<td align=left class=medium_font>".$cc["result"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>response message:</td>
									<td align=left class=medium_font>".$cc["respmsg"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>authorization code:</td>
									<td align=left class=medium_font>".$cc["authcode"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>avs address:</td>
									<td align=left class=medium_font>".$cc["avsaddr"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>avs zip:</td>
									<td align=left class=medium_font>".$cc["avszip"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction tax:</td>
									<td align=left class=medium_font>".$cc["tax"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction amount:</td>
									<td align=left class=medium_font>".$cc["amount"]."</td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
			else
				return false;
			return true;
		}
		else
			return false;
	} //end of function

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_cc_paypal_transaction_details($db,$transaction_id=0,$balance_transaction=0)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "classified_id";
			$this->sql_query = "select * from  geodesic_cc_paypal_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			//echo $this->sql_query." is the query<br>\n";
			if (!$cc_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() == 1)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>PayPal Pro</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>username:</font></td>
									<td class=medium_font>".$user["username"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>first name:</td>
									<td class=medium_font>".$cc["first_name"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>last name:</td>
									<td class=medium_font>".$cc["last_name"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>address:</td>
									<td class=medium_font>".$cc["address"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>city:</td>
									<td class=medium_font>".$cc["city"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>state:</td>
									<td class=medium_font>".$cc["state"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>country:</td>
									<td class=medium_font>".$cc["country"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>zip:</td>
									<td class=medium_font>".$cc["zip"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>email:</td>
									<td class=medium_font>".$cc["email"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>company:</td>
									<td class=medium_font>".$cc["company"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>fax:</td>
									<td class=medium_font>".$cc["fax"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>description:</td>
									<td class=medium_font>".$cc["description"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>AVS code:</td>
									<td class=medium_font>".$cc["avs_code"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>CVV2 code:</td>
									<td class=medium_font>".$cc["cvv2_code"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>PayPal Transaction Id:</td>
									<td class=medium_font>".$cc["trans_id"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Time Stamp:</td>
									<td class=medium_font>".$cc["timestamp"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Ack Code:</td>
									<td class=medium_font>".$cc["ack"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>avs zip:</td>
									<td class=medium_font>".$cc["version"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Build:</td>
									<td class=medium_font>".$cc["build"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Error - Short Message:</td>
									<td class=medium_font>".$cc["error_short_msg"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Error - Long Message:</td>
									<td class=medium_font>".$cc["error_long_msg"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Error Code:</td>
									<td class=medium_font>".$cc["error_code"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>Error Severity Code:</td>
									<td class=medium_font>".$cc["error_severity_code"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction tax:</td>
									<td class=medium_font>".$cc["tax"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>transaction amount:</td>
									<td class=medium_font>".$cc["amount"]."</td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
			else
				return false;
			return true;
		}
		else
			return false;
	} //end of function

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

function display_cc_manual_transaction_details($db,$transaction_id=0,$balance_transaction)
	{
		if ($transaction_id)
		{
			$key = ($balance_transaction) ? "account_balance" : "classified_id";
			$this->sql_query = "select * from  geodesic_cc_payflow_pro_transactions where $key = ".$transaction_id;
			$cc_result = $db->Execute($this->sql_query);
			if (!$cc_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			elseif ($cc_result->RecordCount() > 0)
			{
				$cc = $cc_result->FetchRow();
				$user = $this->get_user_data($db,$cc["user_id"]);
				$this->body .= "
					<tr>
						<td colspan=2>
							<table cellspacing=1 cellpadding=1 border=0 width=100%>
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>payment handler:</td>
									<td align=left class=medium_font><b>Manual</b></td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td width=30% align=right class=medium_font>username:</td>
									<td align=left class=medium_font>".$user["username"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>first name:</td>
									<td align=left class=medium_font>".$cc["first_name"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>last name:</td>
									<td align=left class=medium_font>".$cc["last_name"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>address:</td>
									<td align=left class=medium_font>".$cc["address"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>\n\tcity:</td>
									<td align=left class=medium_font>".$cc["city"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>state:</td>
									<td align=left class=medium_font>".$cc["state"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>country:</td>
									<td align=left class=medium_font>".$cc["country"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>zip:</td>
									<td align=left class=medium_font>".$cc["zip"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>email:</td>
									<td align=left class=medium_font>".$cc["email"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>company:</td>
									<td align=left class=medium_font>".$cc["company"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>fax:</td>
									<td align=left class=medium_font>".$cc["fax"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>description:</td>
									<td align=left class=medium_font>".$cc["description"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>transaction tax:</td>
									<td align=left class=medium_font>".$cc["tax"]."</td>
								</tr>";$this->row_count++;$this->body .= "
								<tr class=".$this->get_row_color().">
									<td align=right class=medium_font>transaction amount:</td>
									<td align=left class=medium_font>".$cc["amount"]."</td>
								</tr>";$this->row_count++;$this->body .= "
							</table>
						</td>
					</tr>";
			}
			else
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Transaction_management
?>
