<? //cc_initiate_2checkout.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

//VARIABLES TO SEND
//sid
//product_id
//quantity
//merchant_order_id
//demo

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
	@mail($this->debug_email,"cc_initiate_2checkout.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
}
if (!$cc_table_result)
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = $this->sql_query;
		@mail($this->debug_email,"cc_initiate_2checkout.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}	
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);	
	$this->display_page($db);
	$db->Close();
	exit;
}

$show_twocheckout = $cc_table_result->FetchRow();

//build redirect
//if ($show_twocheckout["account_type"])
	$cc_url = "https://www2.2checkout.com/2co/buyer/purchase?";
//else
//	$cc_url = "https://www.2checkout.com/cgi-bin/sbuyers/cartpurchase.2c?";
$cc_url .= "sid=".$show_twocheckout["sid"];
$cc_url .= "&fixed=Y";
$cc_url .= "&return_url=".$this->configuration_data["classifieds_url"];
$cc_url .= "&cart_order_id=".$info["trans_id"];
$cc_url .= "&total=".sprintf("%01.2f",$this->total);
$cc_url .= "&c_prod=".$info["trans_id"];
$cc_url .= "&id_type=1";
$cc_url .= "&c_name=".urlencode($info["ad_type"]);
$cc_url .= "&c_description=".urlencode($info["ad_type"]);
$cc_url .= "&c_price=".sprintf("%01.2f",$this->total);
$cc_url .= "&c_tangible=n";
if ($show_twocheckout["demo_mode"])
	$cc_url .= "&demo=Y";
$cc_url .= "&card_holder_name=".urlencode($user_data["firstname"]." ".$user_data["lastname"]);
$cc_url .= "&street_address=".urlencode($user_data["address"]." ".$user_data["address_2"]);
$cc_url .= "&city=".urlencode($user_data["city"]);
$cc_url .= "&state=".urlencode($user_data["state"]);
$cc_url .= "&zip=".urlencode($user_data["zip"]);
$cc_url .= "&country=".urlencode($user_data["country"]);
$cc_url .= "&email=".urlencode($user_data["email"]);
$cc_url .= "&phone=".urlencode($user_data["phone"]);
$cc_url .= "&merchant_order_id=".$info["trans_id"];

if (strlen($debug_email) > 0)
	@mail($debug_email,"pre - 3",$cc_url);
//echo $cc_url."<bR>\n";
header("Location: ".$cc_url);
exit;
?>