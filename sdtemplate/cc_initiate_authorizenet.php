<? //cc_initiate_authorizenet.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

require_once './classes/site_class.php';

$this->subscription_renewal = $this->subscription_renewal;
$this->account_balance = $this->account_balance;

$this->debug_email = "";
$email_msg = "";
$additional_msg = "";
$handler_error_response = "";

$info = Site::cc_pre_process($db,$cc);

$this->sql_query = "select * from ".$cc->CC_TABLE;
$cc_table_result = $db->Execute($this->sql_query);
if (strlen($this->debug_email) > 0)
{
	@mail($this->debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__,$this->sql_query."\n\n".date("l dS of F Y h:i:s A"));
}
if (!$cc_table_result)
{
	if (strlen($this->debug_email) > 0)
	{
		$email_msg = $this->sql_query;
		@mail($this->debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
	}	
	//DISPLAY ERROR PAGE
	$this->body .= Site::cc_success_failure($db,0,$additional_msg);	
	$this->display_page($db);
	$db->Close();
	exit;
}
$show_authorizenet = $cc_table_result->FetchRow();

//*****************************************************************************
//AUTHORIZE.NET SPECIFIC CODE

if ($show_authorizenet["connection_type"] == 1)
{
	if (strlen($debug_email) > 0)
	{
		@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__,"sim method used\n\n".date("l dS of F Y h:i:s A"));
	}
	//do the SIM connection
	$currenttime = $this->shifted_time($db);
	srand($this->shifted_time($db));
	$sequence = rand(1, 1000);

	$data = $show_authorizenet["merchant_login"] ."^". $sequence ."^". $currenttime ."^". $this->total ."^". $show_authorizenet["currency_code"];
	$key = $show_authorizenet["transaction_key"];
	$fingerprint = bin2hex(mhash(MHASH_MD5, $data, $key));
	$return_url = str_replace($this->configuration_data["classifieds_file_name"], "cc_process_authorizenet.php",$this->configuration_data["classifieds_url"]);

	if (strlen($debug_email) > 0)
	{
		@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - return url - sim",$return_url."\n\n".date("l dS of F Y h:i:s A"));
	}

	$cc_url = "https://secure.authorize.net/gateway/transact.dll?";
	$cc_url .= "x_FP_Hash=".$fingerprint;
	$cc_url .= "&x_FP_Sequence=".$sequence;
	$cc_url .= "&x_FP_Timestamp=".$currenttime;
	//$cc_url .= "&x_show_form=PAYMENT+FORM";
	$cc_url .= "&x_Relay_Response=TRUE";
	$cc_url .= "&x_Relay_URL=".urlencode($return_url);
	$cc_url .= "&x_First_Name=".urlencode($user_data->FIRSTNAME);
	$cc_url .= "&x_Last_Name=".urlencode($user_data->LASTNAME);
	if (strlen(trim($user_data->COMPANY_NAME)) > 0)
		$cc_url .= "&x_Company=".urlencode($user_data->COMPANY_NAME);
	$cc_url .= "&x_Address=".urlencode($user_data->ADDRESS." ".$user_data->ADDRESS_2);
	$cc_url .= "&x_City=".urlencode($user_data->CITY);
	$cc_url .= "&x_State=".urlencode($user_data->STATE);
	$cc_url .= "&x_Country=".urlencode($user_data->COUNTRY);
	$cc_url .= "&x_Zip=".urlencode($user_data->ZIP);
	$cc_url .= "&x_Phone=".urlencode($user_data->PHONE);
	if ($user_data->BUSINESS_TYPE == 1)
		$cc_url .= "&x_Customer_Organization_Type=I";
	else
		$cc_url .= "&x_Customer_Organization_Type=B";
	if (strlen(trim($user_data["fax"])) > 0)
		$cc_url .= "&x_Fax=".urlencode($user_data["fax"]);
	$cc_url .= "&x_Cust_ID=".$this->classified_user_id;
	if ($show_authorizenet["send_email_customer"])
		$cc_url .= "&x_Email_Customer=TRUE";
	if ($show_authorizenet["send_email_merchant"])
		$cc_url .= "&x_Merchant_Email=".urlencode($this->configuration_data["site_email"]);
	$cc_url .= "&x_PO_Num=".$info["trans_id"];
	if ($this->invoice_id)
		$cc_url .= "&x_Invoice_Num=".$this->invoice_id;
	elseif ($this->account_balance)
		$cc_url .= "&x_Invoice_Num=".$this->account_balance;
	elseif ($this->subscription_renewal)
		$cc_url .= "&x_Invoice_Num=".$this->subscription_renewal;
	else
		$cc_url .= "&x_Invoice_Num=".$this->classified_id;
	$cc_url .= "&x_Description=".urlencode($info["ad_type"]);
	$cc_url .= "&x_Amount=".$this->total;
	$cc_url .= "&x_currency_code=".$show_authorizenet["currency_code"];
	$cc_url .= "&x_Type=AUTH_CAPTURE";
	$cc_url .= "&x_Card_Num=".$info["cc_number"];
	$cc_url .= "&x_Exp_Date=".sprintf("%02d",$info["cc_exp_month"]).sprintf("%02d",$info["cc_exp_year"]);
	$cc_url .= "&x_Tax=".$this->tax;
	if (strlen(trim($this->classified_variables["discount_code"])) > 0)
	{
		$this->sql_query = "select * from ".$this->discount_codes_table." where
			discount_code = \"".urlencode(trim($this->classified_variables["discount_code"]))."\"
			and active = 1";
		$discount_check_result =  $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$discount_check_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3501];
			return false;
		}
		elseif ($discount_check_result->RecordCount() == 1)
		{
			$show_email = $discount_check_result->FetchRow();
			if (strlen(trim($show_email["discount_email"])) > 0)
				$cc_url .= "&x_Email=".urlencode($show_email["discount_email"]);
			else
				$cc_url .= "&x_Email=".urlencode($user_data->EMAIL);
		}
		else
			$cc_url .= "&x_Email=".urlencode($user_data->EMAIL);
	}
	else
		$cc_url .= "&x_Email=".urlencode($user_data->EMAIL);
	$cc_url .= "&x_Customer_IP=".getenv("REMOTE_ADDR");
	$cc_url .= "&x_Login=".$show_authorizenet["merchant_login"];
	if ($this->renew_upgrade)
		$this->remove_renew_upgrade_session($db,$this->session_id);
	if (strlen($debug_email) > 0)
	{
		@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - cc_url - sim",$cc_url."\n\n".date("l dS of F Y h:i:s A"));
	}
	header("Location: ".$cc_url);
}
elseif ($show_authorizenet["connection_type"] == 2)
{
	if (strlen($debug_email) > 0)
	{
		@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - aim","AIM method used\n\n".date("l dS of F Y h:i:s A"));
	}
	//do the AIM connection
	//post urls
	//authorize.net -- https://secure.authorize.net/gateway/transact.dll
	//planet payment -- https://secure.planetpayment.com/gateway/transact.dll
	//quickcommerce -- https://secure.quickcommerce.net/gateway/transact.dll

	if ($show_authorizenet["merchant_type"] == 1)
		$post_url = "https://secure.authorize.net/gateway/transact.dll";
	elseif ($show_authorizenet["merchant_type"] == 2)
		$post_url = "https://secure.planetpayment.com/gateway/transact.dll";
	elseif ($show_authorizenet["merchant_type"] == 3)
		$post_url = "https://secure.quickcommerce.net/gateway/transact.dll";
	else
	{
		//something is wrong - probably authorize.net anyway
		$post_url = "https://secure.authorize.net/gateway/transact.dll";
	}

	$cc_url = "x_First_Name=".urlencode($user_data->FIRSTNAME);
	$cc_url .= "&x_Last_Name=".urlencode($user_data->LASTNAME);
	if (strlen(trim($user_data->COMPANY_NAME)) > 0)
		$cc_url .= "&x_Company=".urlencode($user_data->COMPANY_NAME);
	$cc_url .= "&x_Address=".urlencode($user_data->ADDRESS." ".$user_data->ADDRESS_2);
	$cc_url .= "&x_City=".urlencode($user_data->CITY);
	$cc_url .= "&x_State=".urlencode($user_data->STATE);
	$cc_url .= "&x_Country=".urlencode($user_data->COUNTRY);
	$cc_url .= "&x_Zip=".urlencode($user_data->ZIP);
	$cc_url .= "&x_Phone=".urlencode($user_data->PHONE);
	if (strlen(trim($this->classified_variables["discount_code"])) > 0)
	{
		$this->sql_query = "select * from ".$this->discount_codes_table." where
			discount_code = \"".urlencode(trim($this->classified_variables["discount_code"]))."\"
			and active = 1";
		$discount_check_result =  $db->Execute($this->sql_query);
		//echo $this->sql_query." is the query<br>\n";
		if (!$discount_check_result)
		{
			//echo $this->sql_query." is the query<br>\n";
			$this->error_message = $this->messages[3501];
			return false;
		}
		elseif ($discount_check_result->RecordCount() == 1)
		{
			$show_email = $discount_check_result->FetchRow();
			if (strlen(trim($show_email["discount_email"])) > 0)
				$cc_url .= "&x_Email=".urlencode($show_email["discount_email"]);
			else
				$cc_url .= "&x_Email=".urlencode($user_data->EMAIL);
		}
		else
			$cc_url .= "&x_Email=".urlencode($user_data->EMAIL);
	}
	else
		$cc_url .= "&x_Email=".urlencode($user_data->EMAIL);
	if ($user_data->BUSINESS_TYPE == 1)
		$cc_url .= "&x_Customer_Organization_Type=I";
	else
		$cc_url .= "&x_Customer_Organization_Type=B";
	$cc_url .= "&x_Type=AUTH_CAPTURE";
	//$cc_url .= "&x_Delim_Data=TRUE";
	if ($show_authorizenet["send_email_customer"])
		$cc_url .= "&x_Email_Customer=TRUE";
	if ($show_authorizenet["send_email_merchant"])
		$cc_url .= "&x_Merchant_Email=".urlencode($this->configuration_data["site_email"]);
	//$cc_url .= "&x_Delim_Char=|";
	//$cc_url .= "&x_Test_Request=TRUE";
	//$cc_url .= "&x_Relay_Response=False";
	$cc_url .= "&x_Description=".urlencode($info["ad_type"]);
	$cc_url .= "&x_Version=3.1";
	$cc_url .= "&x_Method=CC";
	$cc_url .= "&x_PO_Num=".$info["trans_id"];
	if ($this->invoice_id)
		$cc_url .= "&x_Invoice_Num=".$this->invoice_id;
	elseif ($this->account_balance)
		$cc_url .= "&x_Invoice_Num=".$this->account_balance;
	elseif ($this->subscription_renewal)
		$cc_url .= "&x_Invoice_Num=".$this->subscription_renewal;
	else
		$cc_url .= "&x_Invoice_Num=".$this->classified_id;
	$cc_url .= "&x_Customer_IP=".getenv("REMOTE_ADDR");
	$cc_url .= "&x_Tran_Key=".$show_authorizenet["transaction_key"];
	$cc_url .= "&x_Amount=".$this->total;
    $cc_url .= "&x_Login=".$show_authorizenet["merchant_login"];
    $cc_url .= "&x_Password=".$show_authorizenet["merchant_password"];
    $cc_url .= "&x_Cust_ID=".$this->classified_user_id;
    $cc_url .= "&x_Card_Num=".$info["cc_number"];
    $cc_url .= "&x_Exp_Date=".sprintf("%02d",$info["cc_exp_month"]).sprintf("%02d",$info["cc_exp_year"]);

	if (strlen($debug_email) > 0)
	{
		@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - cc_url - aim",$cc_url."\n\n".date("l dS of F Y h:i:s A"));
	}
	$ch = curl_init();
 	curl_setopt($ch, CURLOPT_REFERER, $this->configuration_data["classifieds_url"]);
 	curl_setopt($ch, CURLOPT_URL, "https://secure.authorize.net/gateway/transact.dll");
 	curl_setopt ($ch, CURLOPT_POST, 1);
 	curl_setopt ($ch, CURLOPT_POSTFIELDS, $cc_url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

    //curl_setopt ($ch, CURLOPT_VERBOSE, 1);

    $authorizenet_result = curl_exec ($ch);
    curl_close ($ch);
    if (!$authorizenet_result)
	{
		//bad or no return
		if (strlen($debug_email) > 0)
		{
			@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - no return - aim","there was an error in talking to authorize.net.\n\nnothing was returned from authorize.net\n
				".$this->subscription_renewal." is subscription_renewal\n".$this->renew_upgrade." is renew_upgrade\n".$this->account_balance." is account_balance\n".$this->invoice_id." is invoice_id\n\n".date("l dS of F Y h:i:s A"));
		}
		if ($this->subscription_renewal)
			echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=24&credit_approval=2&declined=no+communication+response\">";
		elseif ($this->renew_upgrade)
			echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=7&credit_approval=2&declined=no+communication+response\">";
		elseif ($this->account_balance)
			echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=29&credit_approval=2&declined=no+communication+response\">";
		elseif ($this->invoice_id)
			echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=30&credit_approval=2&declined=no+communication+response\">";
		else
		{
			header("Location: ".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=2&declined=no+communication+response");
			//echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=2&declined=no+communication+response\">";
			if (strlen($debug_email) > 0)
			{
				@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - no return - aim - redirecting","trying to redirect to the following url: ".$this->configuration_data["classifieds_url"]."?a=1&credit_approval=2&declined=no+communication+response\n\n".date("l dS of F Y h:i:s A"));
			}			
		}
		exit;
	}
	else
	{
		//response returned -- process the results
		$resultarray = explode("|", $authorizenet_result);
		//foreach ($resultarray as $returnvalue)
		//{
		//	$temp .= "|".$returnvalue;
		//}
		if (count($resultarray) > 0)
		{
			$resultFields = Array(
				"x_response_code",
				"x_response_subcode",
				"x_response_reason_code",
				"x_response_reason_text",
				"x_auth_code",
				"x_avs_code",
				"x_trans_id",
				"x_invoice_num",
				"x_description",
				"x_amount",
				"x_method",
				"x_type",
				"x_cust_id",
				"x_first_name",
				"x_last_name",
				"x_company",
				"x_address",
				"x_city",
				"x_state",
				"x_zip",
				"x_country",
				"x_phone",
				"x_fax",
				"x_email",
				"x_ship_to_first_name",
				"x_ship_to_last_name",
				"x_ship_to_company",
				"x_ship_to_address",
				"x_ship_to_city",
				"x_ship_to_state",
				"x_ship_to_zip",
				"x_ship_to_country",
				"x_tax",
				"x_duty",
				"x_freight",
				"x_tax_exempt",
				"x_po_num",
				"x_md5_hash");

			reset($resultarray);
			foreach ($resultarray as $key => $value)
			{
				$this->transaction_results[$resultFields[$key]] = $value;
				$temp .= $resultFields[$key]." = ".$value."\n";
			}

			if (strlen($debug_email) > 0)
			{
				@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - aim - variables returned",$temp."\n\n".date("l dS of F Y h:i:s A"));
			}

			$sql_query = "update ".$cc->CC_TRANSACTION_TABLE." set
				response_code = \"".$this->transaction_results["x_response_code"]."\",
				response_subcode = \"".$this->transaction_results["x_response_subcode"]."\",
				response_reason_code = \"".$this->transaction_results["x_response_reason_code"]."\",
				response_reason_text = \"".$this->transaction_results["x_response_reason_text"]."\",
				auth_code = \"".$this->transaction_results["x_auth_code"]."\",
				avs_code = \"".$this->transaction_results["x_avs_code"]."\",
				trans_id = \"".$this->transaction_results["x_trans_id"]."\"
				where authorizenet_transaction_id = ".$info["trans_id"];
			$result = $db->Execute($sql_query);
			if (strlen($debug_email) > 0)
			{
				$message_body = "x_invoice_num - ".$this->transaction_results["x_invoice_num"]."\n";
				$message_body .= "x_po_num - ".$this->transaction_results["x_po_num"]."\n";
				$message_body .= "x_cust_id - ".$this->transaction_results["x_cust_id"]."\n";
				$message_body .= "x_email - ".$this->transaction_results["x_email"]."\n";
				@mail($debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - aim",$message_body."\n\n".$sql_query."\n\n".date("l dS of F Y h:i:s A"));
			}
			if (!$result)
			{
				if (strlen($this->debug_email) > 0)
				{
					$email_msg = $this->sql_query;
					@mail($this->debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
				}						
				//DISPLAY ERROR PAGE
				$this->body .= Site::cc_success_failure($db,0,$additional_msg);	
				$this->display_page($db);
				$db->Close();
				exit;
			}

			if ($this->transaction_results["x_response_code"] == 1)
				Site::cc_post_process($db,$cc,$info);
			else 
			{
				$handler_error_response = ($this->transaction_results["x_response_reason_text"]) 
					? $this->transaction_results["x_response_reason_text"] 
					: "INTERNAL FAILURE";
			 	Site::cc_post_process($db,$cc,$info,$handler_error_response);
			}
		}
		if (strlen($this->debug_email) > 0)
		{
			@mail($this->debug_email,"cc_initiate_authorizenet.php LINE ".__LINE__." - error",$email_msg."\n\n".$db->ErrorMsg()."\n\n".date("l dS of F Y h:i:s A"));
		}						
		//DISPLAY ERROR PAGE
		$this->body .= Site::cc_success_failure($db,0,$additional_msg);	
		$this->display_page($db);
		$db->Close();
		exit;
	}
}
//end of cc_initiate_authorizenet.php
?>
