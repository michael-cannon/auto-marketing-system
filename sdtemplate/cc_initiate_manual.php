<? //cc_initiate_manual.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$debug_cc_manual = 0;

$this->subscription_renewal = $subscription_renewal;
$this->account_balance = $account_balance;

$this->debug_email = "";
$email_msg = "";
$additional_msg = "";
$handler_error_response = "";
if ($debug_cc_manual)
	echo "<br>about to call cc_pre_process";
Site::cc_pre_process($db,$cc);
if ($debug_cc_manual)
	echo "<br>successfully returned from cc_pre_process";
$this->page_id = 14;
$this->get_text($db);
$this->body .="
	<table cellpadding=2 cellspacing=1 border=0 align=center width=100%>
		<tr class=place_ad_section_title>
			<td>".urldecode($this->messages[1365])."</td>
		</tr>
		<tr class=page_title>
			<td>".urldecode($this->messages[177])."</td>
		</tr>
		<tr class=page_description>
			<td>".urldecode($this->messages[180])."</td>
		</tr>
		<tr class=page_description>
			<td>".urldecode($this->messages[3274]).$this->configuration_data["precurrency"]." ".sprintf("%01.2f",$this->total)." ".$this->configuration_data["postcurrency"]."</td>
		</tr>
	</table>";
if ($debug_cc_manual) echo "<br><br>PAGE ID - $this->page_id";
if ($this->subscription_renewal)
	$this->remove_renew_subscription_session($db,$this->session_id);
elseif ($this->renew_upgrade)
	$this->remove_renew_upgrade_session($db,$this->session_id);
elseif ($this->account_balance)
	$this->remove_account_session($db,$this->session_id);
elseif ($this->invoice_id)
	$this->remove_invoice_session($db,$this->session_id);
else
	$this->remove_sell_session($db,$this->session_id);
$this->display_page($db);
$db->Close();
exit;
?>
