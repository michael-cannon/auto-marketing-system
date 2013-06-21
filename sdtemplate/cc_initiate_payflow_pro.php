<? //cc_initiate_payflow_pro.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

require_once './classes/site_class.php';

$this->subscription_renewal = $subscription_renewal;
$this->account_balance = $account_balance;

$this->debug_email = "";
$email_msg = "";
$additional_msg = "";
$handler_error_response = "";

$info = Site::cc_pre_process($db,$cc);

$this->sql_query = "select * from ".$cc->CC_TABLE;
$cc_table_result = $db->Execute($this->sql_query);
if (strlen($this->debug_email) > 0)
{
	@mail($this->debug_email,"cc_initiate_payflow_pro.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
}
if (!$cc_table_result)
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = $this->sql_query;
		@mail($this->debug_email,"cc_initiate_payflow_pro.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}	
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);	
	$this->display_page($db);
	$db->Close();
	exit;
}
$show_payflow_pro = $cc_table_result->FetchRow();

//*****************************************************************************
//PAYFLOW SPECIFIC CODE

//HostAddress
//HostPort
//ParmList
//--TRXTYPE=S
//--TENDER=C
//--PARTNER=partner (default: VeriSign)
//--VENDOR=login
//--USER=login
//--PWD=password
//--ACCT=cc number
//--EXPDATE=1299
//--AMT=1.00

$HostAddress = ($show_payflow_pro["demo_mode"]) ? "test-payflow.verisign.com" : "payflow.verisign.com";
$HostPort = 443;
$user = ($show_payflow_pro["user"]) ? $show_payflow_pro["user"] : $show_payflow_pro["vendor"];

$transaction = array(	
	"TRXTYPE" => "S",
	"TENDER" => "C",
	"PARTNER" => $show_payflow_pro["partner"],
	"VENDOR" => $show_payflow_pro["vendor"],
	"USER" => $user,
	"PWD" => $show_payflow_pro["password"],
	"ACCT" => $info["cc_number"],
	"EXPDATE" => $info["cc_exp_month"].substr($info["cc_exp_year"], -2),
	"AMT" => $this->total,
	"STREET" => $user_data->ADDRESS,
	"ZIP" => $user_data->ZIP
	);
putenv("PFPRO_CERT_PATH=".dirname(__FILE__)."/pfp_cert");
pfpro_init();
$payflow_pro_result = pfpro_process($transaction,$HostAddress,$HostPort);
if (strlen($this->debug_email) > 0)
{
	$email_msg = highlight_string(print_r($payflow_pro_result,1));
	@mail($this->debug_email,"cc_initiate_payflow_pro.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
}
pfpro_cleanup();
$payflow_pro_result["RESULT"] = 0;
if (!$payflow_pro_result)
{
	//bad or no return
	echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data['classifieds_url']."?a=1&credit_approval=2&declined=no+communication+response\">";
	if ($debug_cc_pfp) echo "<br><br><font color=red>ERROR<br>FILE - <b>".__FILE__."</b><br>LINE - <b>".__LINE__."</b><br>LAST QUERY - $this->sql_query</font><br><br>";
	exit;
}
else
{
	//PNREF - payment network Reference ID...transaction number (numeric - 12)
	//RESULT - 0=approved (number)
	//	any other number indicates a decline or error
	//RESPMSG - response message returned with the transaction result. (variable)
	//AUTHCODE - transactions approved by the bank will receive an authorization code

	//FOR LATER USE
	//AVSADDR - Y=match
	//	N=no match
	//	X=service unavailable or not computed
	//AVSZIP - Y=match
	//	N=no match
	//	X=service unavailable or not computed

	$this->sql_query = "update ".$cc->CC_TRANSACTION_TABLE." set
		pnref = \"".$payflow_pro_result["PNREF"]."\",
		result = \"".$payflow_pro_result["RESULT"]."\",
		respmsg = \"".$payflow_pro_result["RESPMSG"]."\",
		authcode = \"".$payflow_pro_result["AUTHCODE"]."\",
		avsaddr = \"".$payflow_pro_result["AVSADDR"]."\",
		avszip = \"".$payflow_pro_result["AVSZIP"]."\"
		where payflow_pro_transaction_id = ".$info["trans_id"];
	$update_result = $db->Execute($this->sql_query);
	if (strlen($this->debug_email) > 0)
	{
		@mail($this->debug_email,"cc_initiate_payflow_pro.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
	}
	if (!$update_result)
	{
		if (strlen($this->debug_email) > 0)
		{
			$email_msg = $this->sql_query;
			@mail($this->debug_email,"cc_initiate_payflow_pro.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
		}						
		//DISPLAY ERROR PAGE
		$this->body .= Site::cc_success_failure($db,0,$additional_msg);	
		$this->display_page($db);
		$db->Close();
		exit;
	}
	if ($payflow_pro_result["RESULT"] == 0)
		Site::cc_post_process($db,$cc,$info);
	else 
	{
		$handler_error_response = ($payflow_pro_result["RESPMSG"]) ? $payflow_pro_result["RESPMSG"] : "INTERNAL FAILURE";
	 	Site::cc_post_process($db,$cc,$info,$handler_error_response);
	}
}
//end of cc_initiate_payflow_pro.php
?>
