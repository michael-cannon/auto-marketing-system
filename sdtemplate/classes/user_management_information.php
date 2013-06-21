<? //user_management_information.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class User_management_information extends Site
{
	var $registration_configuration;
	var $debug_info = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function User_management_information ($db,$language_id,$classified_user_id=0, $product_configuration=0)
	{
		$this->Site($db,6,$language_id,$classified_user_id, $product_configuration);

		$this->get_registration_configuration_data($db);
	} //end of function User_management_information

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_user_data($db)
	{
		$this->page_id = 37;
		$this->get_text($db);
		if ($this->classified_user_id)
		{
			$user_data = $this->get_user_data($db);
			if ($user_data)
			{
				//display this users information
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td colspan=2 valign=top width=100%>\n\t\t".urldecode($this->messages[637])."\n\t\t</td>\n\t</tr>\n\t";

				$this->body .="<tr class=my_current_info_title>\n\t<td colspan=2>".urldecode($this->messages[554])."\n\t</td>\n\t</tr>\n";

				$this->body .="<tr class=page_description>\n\t<td colspan=2>".urldecode($this->messages[555])."\n\t</td>\n\t</tr>\n";

				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[556]).":\n\t</td>\n\t";
				$this->body .="<td class=data_values>\n\t".$user_data->USERNAME."\n\t</td>\n</tr>\n";
				if (($this->registration_configuration->USE_REGISTRATION_FIRSTNAME_FIELD) || ($this->registration_configuration->USE_REGISTRATION_LASTNAME_FIELD))
				{
					$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[557]).":\n\t</td>\n\t";
					$this->body .="<td class=data_values>\n\t".stripslashes($user_data->FIRSTNAME)." ".stripslashes($user_data->LASTNAME)."\n\t</td>\n</tr>\n";
				}

				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[559]).":\n\t</td>\n\t";
				$this->body .="<td class=data_values>\n\t".urldecode($user_data->EMAIL)."\n\t</td>\n</tr>\n";

				if ($this->registration_configuration->USE_REGISTRATION_EMAIL2_FIELD)
				{
					$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[1403]).":\n\t</td>\n\t";
					$this->body .="<td class=data_values>\n\t".urldecode($user_data->EMAIL2)."\n\t</td>\n</tr>\n";
				}

				if ($this->registration_configuration->USE_REGISTRATION_BUSINESS_TYPE_FIELD)
				{
					$this->body .="<tr><td class=field_labels>".urldecode($this->messages[561])."</td>\n\t";
					$this->body .="<td class=data_values>\n\t\t";
					if ($user_data->BUSINESS_TYPE == 1)
						$this->body .= urldecode($this->messages[1401]);
					elseif ($user_data->BUSINESS_TYPE == 2)
						$this->body .= urldecode($this->messages[558]);
					else
						$this->body .= urldecode($this->messages[1402]);
					$this->body .= "</td>\n\t</tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_COMPANY_NAME_FIELD)
				{
					if (strlen(trim($user_data->COMPANY_NAME)) > 0)
					{
						$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[560])."\n\t</td>\n\t";
						$this->body .="<td class=data_values>\n\t".stripslashes($user_data->COMPANY_NAME)."\n\t</td>\n</tr>\n";
					}
				}


				//$this->body .="<tr>\n\t<td >".$this->medium_font_tag."</font>\n\t</td>\n\t";
				//$this->body .="<td>\n\t".$this->medium_font;
				//if ($user_data->BUSINESS_TYPE == 1)
				//	$this->body .="individual";
				//else
				//	$this->body .="business";
				//$this->body .="</font>\n\t</td>\n</tr>\n";
				if (($this->registration_configuration->USE_REGISTRATION_ADDRESS_FIELD)
					|| ($this->registration_configuration->USE_REGISTRATION_CITY_FIELD)
					|| ($this->registration_configuration->USE_REGISTRATION_STATE_FIELD)
					|| ($this->registration_configuration->USE_REGISTRATION_ZIP_FIELD)
					|| ($this->registration_configuration->USE_REGISTRATION_COUNTRY_FIELD))
				{
					$address_display = 0;
					$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[562]).":\n\t</td>\n\t";
					$this->body .="<td class=data_values>\n\t";
					if ($this->registration_configuration->USE_REGISTRATION_ADDRESS_FIELD)
					{
						$this->body .= stripslashes($user_data->ADDRESS);
						$address_display = 1;
					}
					if (($this->registration_configuration->USE_REGISTRATION_ADDRESS2_FIELD) && (strlen(trim($user_data->ADDRESS_2)) > 0))
					{
						if ($address_display)
						{
							$this->body .="<br>";
							$this->body .= stripslashes($user_data->ADDRESS_2);
						}
						else
						{
							$this->body .= stripslashes($user_data->ADDRESS_2);
							$address_display = 1;
						}
					}
					if ($this->registration_configuration->USE_REGISTRATION_CITY_FIELD)
					{
						if ($address_display)
						{
							$this->body .="<br>";
							$this->body .= stripslashes($user_data->CITY);
						}
						else
						{
							$this->body .= stripslashes($user_data->CITY);
							$address_display = 1;
						}
					}
					if ($this->registration_configuration->USE_REGISTRATION_STATE_FIELD)
					{
						$this->body .=", ".$user_data->STATE;
					}
					if ($this->registration_configuration->USE_REGISTRATION_ZIP_FIELD)
					{
						$this->body .=" ".stripslashes($user_data->ZIP);
					}
					if ($this->registration_configuration->USE_REGISTRATION_COUNTRY_FIELD)
					{
						if ($address_display)
						{
							$this->body .="<br>";
							$this->body .= stripslashes($user_data->COUNTRY);
						}
						else
						{
							$this->body .= stripslashes($user_data->COUNTRY);
							$address_display = 1;
						}
					}
					$this->body .="\n\t</td>\n</tr>\n";
				}

				if (($this->registration_configuration->USE_REGISTRATION_PHONE_FIELD)
					|| ($this->registration_configuration->USE_REGISTRATION_PHONE2_FIELD)
					|| ($this->registration_configuration->USE_REGISTRATION_FAX_FIELD))
				{
					if (($this->registration_configuration->USE_REGISTRATION_PHONE_FIELD) && (strlen(trim($user_data->PHONE)) > 0))
					{
						$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[563]).":\n\t</td>\n\t";
						$this->body .="<td class=data_values>\n\t";
						$this->body .= stripslashes($user_data->PHONE);
						$this->body .= "\n\t</td>\n</tr>\n";
					}
					if (($this->registration_configuration->USE_REGISTRATION_PHONE2_FIELD) && (strlen(trim($user_data->PHONE2)) > 0))
					{
						$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[564]).":\n\t</td>\n\t";
						$this->body .= "<td class=data_values>\n\t";
						$this->body .= stripslashes($user_data->PHONE2);
						$this->body .= "\n\t</td>\n</tr>\n";
					}

					if (($this->registration_configuration->USE_REGISTRATION_FAX_FIELD) && (strlen(trim($user_data->FAX)) > 0))
					{
						$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[565]).":\n\t</td>\n\t";
						$this->body .="<td class=data_values>\n\t";
						$this->body .= stripslashes($user_data->FAX);
						$this->body .= "\n\t</td>\n</tr>\n";
					}
				}
				if (($this->registration_configuration->USE_REGISTRATION_URL_FIELD) && (strlen(trim($user_data->URL)) > 0))
				{
					$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[566]).":\n\t</td>\n\t";
					$this->body .="<td class=data_values>\n\t".stripslashes($user_data->URL)."\n\t</td>\n</tr>\n";
				}







				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_1_FIELD)
				{
					$this->body .="<tr>\n\t\t<td  class=field_labels>".urldecode($this->messages[1241])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_1)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_1)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_2_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1242])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_2)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_2)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_3_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1243])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_3)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_3)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_4_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1244])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_4)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_4)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_5_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1245])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_5)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_5)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_6_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1246])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_6)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_6)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_7_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1247])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_7)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_7)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_8_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1248])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_8)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_8)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_9_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1249])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_9)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_9)."\n\t\t</td></tr>\n\t";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_10_FIELD)
				{
					$this->body .="<tr>\n\t\t<td class=field_labels>".urldecode($this->messages[1250])."</td>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION))
					{
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_10)." ";
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=14>".urldecode($this->messages[1511])."</a>";
					}
					else
						$this->body .="<td class=data_values>\n\t\t".stripslashes($user_data->OPTIONAL_FIELD_10)."\n\t\t</td></tr>\n\t";
				}

				$this->body .="<tr>\n\t<td class=field_labels>".urldecode($this->messages[567]).":\n\t</td>\n\t";
				$this->body .="<td valign=top class=data_values>\n\t".date("M d, Y G:i - l", $user_data->DATE_JOINED)."\n\t</td>\n</tr>\n";

                //BCS-IT 02.05.2006
                if(strlen(trim($user_data->POCFIRSTNAME)) > 0 || strlen(trim($user_data->POCLASTNAME)) > 0 || strlen(trim($user_data->POCEMAIL)) > 0 || strlen(trim($user_data->POCPHONE)) > 0)
                {
                    $this->body .= "<hr size=1 color=#cccccc width=50% align=left>";
                }
                if (strlen(trim($user_data->POCFIRSTNAME)) > 0)
                {
                    $this->body .="<tr>\n\t<td class=field_labels>".urldecode("Inventory POC firstname").":\n\t</td>\n\t";
                    $this->body .="<td class=data_values>\n\t".stripslashes(urldecode($user_data->POCFIRSTNAME))."\n\t</td>\n</tr>\n";
                }
                if (strlen(trim($user_data->POCLASTNAME)) > 0)
                {
                    $this->body .="<tr>\n\t<td class=field_labels>".urldecode("Inventory POC lastname").":\n\t</td>\n\t";
                    $this->body .="<td class=data_values>\n\t".stripslashes(urldecode($user_data->POCLASTNAME))."\n\t</td>\n</tr>\n";
                }
                if (strlen(trim($user_data->POCEMAIL)) > 0)
                {
                    $this->body .="<tr>\n\t<td class=field_labels>".urldecode("Inventory POC email").":\n\t</td>\n\t";
                    $this->body .="<td class=data_values>\n\t".stripslashes(urldecode($user_data->POCEMAIL))."\n\t</td>\n</tr>\n";
                }
                if (strlen(trim($user_data->POCPHONE)) > 0)
                {
                    $this->body .="<tr>\n\t<td class=field_labels>".urldecode("Inventory POC phone").":\n\t</td>\n\t";
                    $this->body .="<td class=data_values>\n\t".stripslashes(urldecode($user_data->POCPHONE))."\n\t</td>\n</tr>\n";
                }
                //BCS-IT 02.05.2006

				//check if allow site account balances
				if ($this->debug_info)
				{
					echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE<Br>\n";
					echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<bR>\n";
				}
				if ($this->configuration_data['use_account_balance'])
				{
					if ($this->configuration_data['positive_balances_only'])
					{
						$this->body .="<tr>\n\t\t<td  class=field_labels>".urldecode($this->messages[2538])."</td>\n\t\t";
						$this->body .="<td class=data_values>\n\t\t".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$user_data->ACCOUNT_BALANCE)." "
							.$this->configuration_data['postcurrency'];
						$this->body .= " <a href=";
						if ($this->configuration_data['use_ssl_in_sell_process'])
							$this->body .=trim($this->configuration_data['classifieds_ssl_url']);
						else
							$this->body .= trim($this->configuration_data['classifieds_file_name']);
						$this->body .="?a=29>".urldecode($this->messages[2539])."</a>\n\t\t</td></tr>\n\t";
					}
					else
					{
						$this->body .="<tr>\n\t\t<td  class=field_labels>".urldecode($this->messages[3243])."</td>\n\t\t";
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
						$this->body .="<td class=data_values>\n\t\t".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$to_be_invoiced)." "
							.$this->configuration_data['postcurrency']."</td></tr>\n";
					}
				}

				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				$user_group_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$user_group_result)
				{
					return false;
				}
				elseif ($user_group_result->RecordCount() == 1)
				{
					$show_user_stuff = $user_group_result->FetchNextObject();

					$this->sql_query = "select * from ".$this->groups_table." where group_id = ".$show_user_stuff->GROUP_ID;
					$group_result = $db->Execute($this->sql_query);
					if (!$group_result)
					{
						echo $this->sql_query."<br>\n";
						return false;
					}
					elseif ($group_result->RecordCount() == 1)
					{
						$group_stuff = $group_result->FetchNextObject();
						if ($group_stuff)
						{
							//current group
							if ($group_stuff->AFFILIATE)
							{
								//show link to edit the html placed on affiliate site
								$this->body .= "<tr>\n\t<td  class=field_labels>".urldecode($this->messages[729]).":</font>\n\t</td>\n\t";
								$this->body .= "<td class=data_values>\n\t<a href=".$this->configuration_data['affiliate_url']."?aff=".$this->classified_user_id." class=data_values>".$this->configuration_data['affiliate_url']."?aff=".$this->classified_user_id."</font></a>\n\t</td>\n</tr>\n";
							}

							//STOREFRONT CODE
							if(file_exists('classes/storefront/store_class.php'))
							{
								if ($group_stuff->STOREFRONT)
								{
									include_once('classes/storefront/store_class.php');

									$this->sql_query = "select * from ".Store::get('storefront_subscriptions_table')." where user_id = ".$this->classified_user_id;
									$subscriptionResult = $db->Execute($this->sql_query);

									if($subscriptionResult->RecordCount()==1)
									{
										$subscriptionInfo = $subscriptionResult->FetchRow();
										$expiresAt = $this->shifted_time($db) + $subscriptionInfo["expiration"];
										if(time()<=$expiresAt)
										{
											//show link to edit the html placed on affiliate site
											$this->body .= "<tr>\n\t<td  class=field_labels>".urldecode($this->messages[500006]).":</font>\n\t</td>\n\t";
											$this->body .= "<td class=data_values>\n\t<a href=".$this->configuration_data['storefront_url']."?store=".$this->classified_user_id." class=data_values>".$this->configuration_data['storefront_url']."?store=".$this->classified_user_id."</font></a>\n\t</td>\n</tr>\n";
										}
									}
								}
							}
							//STOREFRONT CODE

						}
						$this->body .="<tr>\n\t<td align=center colspan=2 class=edit_your_info_link>
							<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=4 class=edit_your_info_link>".urldecode($this->messages[568])."</a>
							\n\t</td>\n</tr>\n";

						// Save default auctions and classifieds price plan id
						$class_price_plan = $group_stuff->PRICE_PLAN_ID;
						$auction_price_plan = $group_stuff->AUCTION_PRICE_PLAN_ID;
					}
				}

			// MLC no show
			if ( false )
			{
				// Price Plan information
				if($this->is_class_auctions())
				{
					// Auctions
					$this->display_price_plan_info($db, $auction_price_plan);

					// Classifieds
					$this->display_price_plan_info($db, $class_price_plan);
				}
				elseif($this->is_auctions())
				{
					$this->display_price_plan_info($db, $auction_price_plan);
				}
				elseif($this->is_classifieds())
				{
					$this->display_price_plan_info($db, $class_price_plan);
				}
				//STOREFRONT CODE
				if(file_exists('classes/storefront/store_class.php'))
				{
					include_once('classes/storefront/store_class.php');
					include_once('classes/storefront/user_management_storefront.php');
					$this->sql_query = "select storefront from $this->groups_table where group_id = ".$user_data->GROUP_ID;
					$result = $db->Execute($this->sql_query);
					if(!$result)
						return false;
					$result = $result->FetchRow();
					if($result["storefront"]==1)
					{
						$displaySubscriptionInfo = User_management_storefront::display_subscription_info($db, $this->classified_user_id);
						if($displaySubscriptionInfo!==false)
							$this->body .= $displaySubscriptionInfo;
					}
				}
				//STOREFRONT CODE
			}


				//$this->body .="<tr>\n\t<td align=center colspan=2 class=edit_your_info_link>
				//	<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=4 class=edit_your_info_link>".urldecode($this->messages[568])."</a>
				//	\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td align=center colspan=2 class=edit_your_info_link>
					<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=edit_your_info_link>".urldecode($this->messages[569])."</a>
					\n\t</td>\n</tr>\n";

				$this->body .="</table>\n";
				$this->display_page($db);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//no user id
			return false;
		}
	} //end of function display_user_data

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_price_plan_info($db, $price_plan_id)
	{
		$this->sql_query = "select * from ".$this->price_plans_table." where price_plan_id = ".$price_plan_id;
		$price_plan_result = $db->Execute($this->sql_query);
		if (!$price_plan_result)
		{
			echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($price_plan_result->RecordCount() == 1)
		{
			$base_price_plan = $price_plan_result->FetchNextObject();

			//echo $base_price_plan->TYPE_OF_BILLING." is type of billing<BR>\n";
			//echo $credits->CREDIT_COUNT." is the credit count<BR>\n";

			//current price plan
			if($base_price_plan->APPLIES_TO == 1)
			{
				$this->body .= "<tr>\n\t<td class=my_current_info_title colspan=2>".urldecode($this->messages[730])."</font>\n\t</td>\n</tr>";
				$this->body .= "<tr>\n\t<td class=page_description colspan=2>".urldecode($this->messages[745])."</font>\n\t</td>\n</tr>";
			}
			elseif($base_price_plan->APPLIES_TO == 2)
			{
				$this->body .= "<tr>\n\t<td class=my_current_info_title colspan=2>".urldecode($this->messages[200006])."</font>\n\t</td>\n</tr>";
				$this->body .= "<tr>\n\t<td class=page_description colspan=2>".urldecode($this->messages[200007])."</font>\n\t</td>\n</tr>";
			}
			$this->body .= "<tr>\n\t<td colspan=2>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>";
			if ($base_price_plan->TYPE_OF_BILLING == 1)
			{
				//charged per ad -- check for credits
				$this->body .= "<tr>\n\t<td  class=field_labels width=50%>".urldecode($this->messages[733]).": </font>\n\t</td>\n\t";
				$this->body .= "<td class=data_values width=50%>\n\t".urldecode($this->messages[732])."\n\t</td>\n</tr>\n";
				//$this->body .= "<tr >\n\t<td  class=field_labels>".urldecode($this->messages[1419]).": </font>\n\t</td>\n\t";
				//$this->body .= "<td class=data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->CHARGE_PER_AD).
				//	" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";


				$credits = $this->get_number_of_user_credits($db);
				if ($credits)
				{
					//show this users credits
					$this->body .= "<tr >\n\t<td  class=field_labels>".urldecode($this->messages[741]).": </font>\n\t</td>\n\t";
					$this->body .= "<td class=data_values>\n\t";
					$this->sql_query = "select * from ".$this->user_credits_table." where user_id = ".$this->classified_user_id." order by credits_expire asc";
					$credits_results = $db->Execute($this->sql_query);
					//echo $this->sql_query."<bR>\n";
					if (!$credits_results)
					{
						return false;
					}
					elseif ($credits_results->RecordCount()  > 0)
					{
						while ($show_credits = $credits_results->FetchNextObject())
						{
							$this->body .= $show_credits->CREDIT_COUNT." ( ".urldecode($this->messages[742])." ".date("M d, Y H:i:s", $show_credits->CREDITS_EXPIRE)." )<Br>";
						}
					}
					$this->body .= "\n\t</td>\n</tr>\n";
				}

				//charge per ad
				if ($base_price_plan->CHARGE_PER_AD_TYPE == 0)
				{
					//flat fee per ad
					$this->body .= "<tr >\n\t<td  class=field_labels>".urldecode($this->messages[1419]).": </font>\n\t</td>\n\t";
					$this->body .= "<td class=data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->CHARGE_PER_AD)." ".$this->configuration_data['postcurrency']."\n\t</td>\n</tr>\n";

				}
				elseif ($base_price_plan->CHARGE_PER_AD_TYPE == 1)
				{
					//fee based on price field
					$this->body .= "<tr >\n\t<td  class=field_labels>".urldecode($this->messages[1419]).": </font>\n\t</td>\n\t";
					$this->body .= "<td class=data_values>\n\t".urldecode($this->messages[1480])."\n\t</td>\n</tr>\n";

				}
				elseif ($base_price_plan->CHARGE_PER_AD_TYPE == 2)
				{
					//fee based on length of ad
					$this->body .= "<tr >\n\t<td  class=field_labels>".urldecode($this->messages[1419]).": </font>\n\t</td>\n\t";
					$this->body .= "<td class=data_values>\n\t".urldecode($this->messages[1481])."\n\t</td>\n</tr>\n";

				}
			}
			elseif ($base_price_plan->TYPE_OF_BILLING == 2)
			{
				//charge by subscription -- display when expire
				$this->body .= "<tr>\n\t<td  class=field_labels width=50%>".urldecode($this->messages[733]).":</font>\n\t</td>\n\t";
				$this->body .= "<td width=50%  class=data_values>\n\t".urldecode($this->messages[731])."</td>\n</tr>\n";
				$subscription = $this->get_user_subscription($db);
				//if ($subscription)
				//{
				//show the expiration date
				if ($subscription)
				{
					$this->body .= "<tr>\n\t<td  class=field_labels>".urldecode($this->messages[743]).": </font>\n\t</td>\n\t";
					$this->body .= "<td class=data_values>\n\t".date("M d, Y H:i:s", $subscription->SUBSCRIPTION_EXPIRE)."\n\t</td>\n</tr>\n";
				}
				else
				{
					$this->body .= "";
					//$this->body .= "<td class=data_values>\n\t".urldecode($this->messages[743])." ".date("M d, Y H:i:s", $subscription->SUBSCRIPTION_EXPIRE)."\n\t</td>\n</tr>\n";
				}
				//}
				$this->body .= "<tr>\n\t<td  class=field_labels width=50%>".urldecode($this->messages[1649]).":</font>\n\t</td>\n\t";
				$this->body .= "<td width=50%  class=data_values>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=24 class=data_values>".urldecode($this->messages[1650])."</td>\n</tr>\n";

			}
			//show extra feature costs
			if ($base_price_plan->CHARGE_PER_PICTURE > 0)
			{
				//charge per picture
				$this->body .="<tr>\n\t\t<td  class=field_labels>".urldecode($this->messages[734])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->CHARGE_PER_PICTURE).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_bolding_feature']) && ($base_price_plan->USE_BOLDING))
			{
				//bolding
				$this->body .="<tr>\n\t\t<td  class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[735])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->BOLDING_PRICE).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_better_placement_feature']) && ($base_price_plan->USE_BETTER_PLACEMENT))
			{
				//better placement
				$this->body .="<tr>\n\t\t<td  class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[736])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->BETTER_PLACEMENT_CHARGE).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_featured_feature']) && ($base_price_plan->USE_FEATURED_ADS))
			{
				//featured ad
				$this->body .="<tr>\n\t\t<td   class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[737])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->FEATURED_AD_PRICE).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_featured_feature_2']) && ($base_price_plan->USE_FEATURED_ADS_LEVEL_2))
			{
				//featured ad
				$this->body .="<tr>\n\t\t<td   class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[2346])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->FEATURED_AD_PRICE_2).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_featured_feature_3']) && ($base_price_plan->USE_FEATURED_ADS_LEVEL_3))
			{
				//featured ad
				$this->body .="<tr>\n\t\t<td   class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[2347])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->FEATURED_AD_PRICE_3).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_featured_feature_4']) && ($base_price_plan->USE_FEATURED_ADS_LEVEL_4))
			{
				//featured ad
				$this->body .="<tr>\n\t\t<td   class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[2348])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->FEATURED_AD_PRICE_4).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_featured_feature_5']) && ($base_price_plan->USE_FEATURED_ADS_LEVEL_5))
			{
				//featured ad
				$this->body .="<tr>\n\t\t<td   class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[2349])."</font></td>\n\t";
				$this->body .="<td  class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->FEATURED_AD_PRICE_5).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
			}

			if (($this->configuration_data['use_attention_getters']) && ($base_price_plan->USE_ATTENTION_GETTERS))
			{
				//attention getters
				$this->body .="<tr>\n\t\t<td   class=field_labels><font class=ad_cost_features_field_labels >".urldecode($this->messages[744])."</font></td>\n\t";
				$this->body .="\n\t\t<td class=data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$base_price_plan->ATTENTION_GETTER_PRICE).
				" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
			}
			$this->body .= "</table>\n\t</td>\n</tr>\n";

			$this->sql_query = "select * from ".$this->final_fee_table." where price_plan_id = ".$base_price_plan->PRICE_PLAN_ID." order by low asc";
			$result = $db->Execute($this->sql_query);
			if (!$result)
				return false;
			elseif ($result->RecordCount() > 0)
			{
				$this->body .= "<tr><td colspan=100% align=center>
								<table cellpadding=3 cellspacing=1 border=0>
									<tr class=final_fee_header>
										<td colspan=100%>".urldecode($this->messages[200123])."</td>
									</tr>
									<tr class=field_labels>
										<td>".urldecode($this->messages[200119])."</td>
										<td>&nbsp;</td>
										<td>".urldecode($this->messages[200120])."</td>
										<td>".urldecode($this->messages[200121])."</td>
									</tr>";
				$this->row_count = 0;
				while ($show = $result->FetchRow())
				{
					$this->body .= "
									<tr class=data_values>
										<td>".$show["low"]."</td>
										<td>to</td>
										<td>";
					if ($show["high"] == 100000000)
						$this->body .= urldecode($this->messages[200122]);
					else
						$this->body .= $show["high"];
					$this->body .= "</td>
										<td>".$show["charge"]." %</td>
									</tr>";
					$this->row_count++;
				}
				$this->body .= "
								</table>
							</td></tr>";
			}

			//get subcategory pricing
			$this->sql_query = "select * from ".$this->price_plans_categories_table." where price_plan_id = ".$price_plan_id;
			//echo $this->sql_query."<bR>\n";
			$category_price_plan_result = $db->Execute($this->sql_query);
			if (!$category_price_plan_result)
			{
				return false;
			}
			elseif ($category_price_plan_result->RecordCount() > 0)
			{
				$this->body .= "<tr>\n\t<td colspan=2 class=my_current_info_title>".urldecode($this->messages[738])."</font>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2 class=page_description>".urldecode($this->messages[739])."</font>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td colspan=2>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>";
				while ($show_category = $category_price_plan_result->FetchNextObject())
				{
					$category_name = $this->get_category_name($db,$show_category->CATEGORY_ID);
					$this->body .= "<tr>\n\t<td colspan=2 class=category_coverage_label>".$category_name->CATEGORY_NAME." ".urldecode($this->messages[740])."</font>\n\t</td>\n</tr>\n";
					$this->body .= "<td>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>";

					//echo $show_category->CHARGE_PER_AD_TYPE." is charge per ad type<br>\n";
					//echo $base_price_plan->TYPE_OF_BILLING." is type<br>\n";
					if ($base_price_plan->TYPE_OF_BILLING == 1)
					{
						//charged per ad -- check for credits
						$this->body .= "<tr>\n\t<td  class=category_field_label width=50%>".urldecode($this->messages[733]).": \n\t</td>\n\t";
						$this->body .= "<td class=category_data_values width=50%>\n\t".urldecode($this->messages[732])."\n\t</td>\n</tr>\n";

						//charge per ad
						//echo $show_category->CHARGE_PER_AD_TYPE." is charge per ad type<br>\n";
						if ($show_category->CHARGE_PER_AD_TYPE == 0)
						{
							//flat fee per ad
							$this->body .= "<tr >\n\t<td  class=category_field_label>".urldecode($this->messages[1419]).": </font>\n\t</td>\n\t";
							$this->body .= "<td class=category_data_values>\n\t".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->CHARGE_PER_AD)." ".$this->configuration_data['postcurrency']."\n\t</td>\n</tr>\n";
						}
						elseif ($show_category->CHARGE_PER_AD_TYPE == 1)
						{
							//fee based on price field
							$this->body .= "<tr >\n\t<td  class=category_field_label>".urldecode($this->messages[1419]).": </font>\n\t</td>\n\t";
							$this->body .= "<td class=category_data_values>\n\t".urldecode($this->messages[1480])."\n\t</td>\n</tr>\n";
						}
						elseif ($show_category->CHARGE_PER_AD_TYPE == 2)
						{
							//fee based on length of ad
							$this->body .= "<tr >\n\t<td  class=category_field_label>".urldecode($this->messages[1419]).": </font>\n\t</td>\n\t";
							$this->body .= "<td class=category_data_values>\n\t".urldecode($this->messages[1481])."\n\t</td>\n</tr>\n";

						}

					}
					elseif ($base_price_plan->TYPE_OF_BILLING == 2)
					{
						//charge by subscription -- display when expire
						$this->body .= "<tr>\n\t<td  class=category_field_label width=50%>".urldecode($this->messages[733]).":\n\t</td>\n\t";
						$this->body .= "<td class=category_data_values width=50% >\n\t".urldecode($this->messages[731])."</td>\n</tr>\n";
					}
					if ($show_category->CHARGE_PER_PICTURE > 0)
					{
						//charge per picture
						$this->body .="<tr>\n\t\t<td  class=category_field_label width=50%>".urldecode($this->messages[734])."</td>\n\t";
						$this->body .="<td class=category_data_values width=50% >".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->CHARGE_PER_PICTURE).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_bolding_feature'])
					{
						//bolding
						$this->body .="<tr>\n\t\t<td  class=category_field_label width=50%>".urldecode($this->messages[735])."</td>\n\t";
						$this->body .="<td class=category_data_values width=50% >".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->BOLDING_PRICE).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_better_placement_feature'])
					{
						//better placement
						$this->body .="<tr>\n\t\t<td  class=category_field_label width=50%>".urldecode($this->messages[736])."</td>\n\t";
						$this->body .="<td  class=category_data_values width=50% >".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->BETTER_PLACEMENT_CHARGE).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_featured_feature'])
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td   class=category_field_label width=50%>".urldecode($this->messages[737])."</td>\n\t";
						$this->body .="<td  class=category_data_values width=50% >".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->FEATURED_AD_PRICE).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_featured_feature_2'])
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td   class=category_field_label><font class=ad_cost_features_field_labels >".urldecode($this->messages[2346])."</font></td>\n\t";
						$this->body .="<td  class=category_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->FEATURED_AD_PRICE_2).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_featured_feature_3'])
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td   class=category_field_label><font class=ad_cost_features_field_labels >".urldecode($this->messages[2347])."</font></td>\n\t";
						$this->body .="<td  class=category_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->FEATURED_AD_PRICE_3).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_featured_feature_4'])
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td   class=category_field_label><font class=ad_cost_features_field_labels >".urldecode($this->messages[2348])."</font></td>\n\t";
						$this->body .="<td  class=category_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->FEATURED_AD_PRICE_4).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_featured_feature_5'])
					{
						//featured ad
						$this->body .="<tr>\n\t\t<td   class=category_field_label><font class=ad_cost_features_field_labels >".urldecode($this->messages[2349])."</font></td>\n\t";
						$this->body .="<td  class=category_data_values>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->FEATURED_AD_PRICE_5).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n\t";
					}

					if ($this->configuration_data['use_attention_getters'])
					{
						//attention getters
						$this->body .="<tr>\n\t\t<td   class=category_field_label width=50%><font class=ad_cost_features_field_labels >".urldecode($this->messages[744])."</font></td>\n\t";
						$this->body .="\n\t\t<td class=category_data_values  width=50%>".$this->configuration_data['precurrency']." ".sprintf("%01.2f",$show_category->ATTENTION_GETTER_PRICE).
						" ".$this->configuration_data['postcurrency']." </td>\n\t</tr>\n";
					}
					$this->body .= "</table>\n\t</td>\n</tr>\n";
				}
				$this->body .= "</table>\n\t</td>\n</tr>\n";
			}
		}
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_user_form($db,$info=0)
	{
		$this->page_id = 38;
		$this->get_text($db);
		if (($this->classified_user_id) && ($this->classified_user_id != 1))
		{
			$this->sql_query = "select * from ".$this->userdata_table." where id = ".$this->classified_user_id;
			if ($this->debug_info) echo $this->sql_query."<br>\n";
			$result = $db->Execute($this->sql_query);

			if (!$result)
			{
				if ($this->debug_info) echo $this->sql_query."<br>\n";
				$this->site_error($this->sql_query,$db->ErrorMsg());
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchNExtObject();

				//get password
				$this->sql_query = "select password from ".$this->logins_table." where id = ".$this->classified_user_id;
				$password_result = $db->Execute($this->sql_query);
				if ($this->debug_info) echo $this->sql_query."<br>\n";
				if (!$password_result)
				{
					if ($this->debug_info) echo $this->sql_query."<br>\n";
					$this->site_error($this->sql_query,$db->ErrorMsg());
					return false;
				}
				elseif ($password_result->RecordCount() == 1)
				{
					$show_password = $password_result->FetchNextObject();
				}
				else
				{
					if ($this->debug_info) echo $this->sql_query."<br>\n";
					$this->site_error($this->sql_query,$db->ErrorMsg());
					return false;
				}

				if (!$info)
				{
					$info = array();
					$info[company_name] = stripslashes($show->COMPANY_NAME);
					$info[firstname] = stripslashes($show->FIRSTNAME);
					$info[lastname] = stripslashes($show->LASTNAME);
					$info[address] = stripslashes($show->ADDRESS);
					$info[address_2] = stripslashes($show->ADDRESS_2);
					$info[city] = stripslashes($show->CITY);
					$info[state] = $show->STATE;
					$info[country] = $show->COUNTRY;
					$info[zip] = stripslashes($show->ZIP);
					$info[phone] = stripslashes($show->PHONE);
					$info[phone_2] = stripslashes($show->PHONE2);
					$info[fax] = stripslashes($show->FAX);
					$info[url] = stripslashes($show->URL);
					$info[email] = $show->EMAIL;
					$info[password] = $show_password->PASSWORD;
					$info[password_verify] = $show_password->PASSWORD;
					$info[affiliate_html] = stripslashes($show->AFFILIATE_HTML);
					$info[optional_field_1] = stripslashes($show->OPTIONAL_FIELD_1);
					$info[optional_field_2] = stripslashes($show->OPTIONAL_FIELD_2);
					$info[optional_field_3] = stripslashes($show->OPTIONAL_FIELD_3);
					$info[optional_field_4] = stripslashes($show->OPTIONAL_FIELD_4);
					$info[optional_field_5] = stripslashes($show->OPTIONAL_FIELD_5);
					$info[optional_field_6] = stripslashes($show->OPTIONAL_FIELD_6);
					$info[optional_field_7] = stripslashes($show->OPTIONAL_FIELD_7);
					$info[optional_field_8] = stripslashes($show->OPTIONAL_FIELD_8);
					$info[optional_field_9] = stripslashes($show->OPTIONAL_FIELD_9);
					$info[optional_field_10] = stripslashes($show->OPTIONAL_FIELD_10);

					if ($this->debug_info)
					{
						echo $info[state]." is info state<Br>\n";
						echo $show->STATE." is the state out of datbase<BR>\n";
					}
				}

				//get this users info and show the form
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=4 method=post>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top colspan=3 width=100%>\n\t\t".urldecode($this->messages[636])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=edit_info_title>\n\t<td colspan=3 >\n\t".urldecode($this->messages[514])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description  >\n\t<td colspan=3>\n\t".urldecode($this->messages[515])."\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[516])."\n\t</td>\n\t";
				$this->body .="<td class=data_values>".$show->USERNAME."\n\t</td>\n<td class=field_labels>&nbsp;</td></tr>\n";

				$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[517])."\n\t</td>\n\t";
				$this->body .="<td class=data_values><input type=password name=c[password] value=\"".$info[password]."\">";
				if (isset($this->error["password"]))
					$this->body .= "<font class=error_message>".urldecode($this->error["password"])."</font>";
				$this->body .="</td>\n<td class=field_labels>&nbsp;</td></tr>\n";
				$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[518])."\n\t</td>\n\t";
				$this->body .="<td class=data_values><input type=password name=c[password_verify] value=\"".$info[password_verify]."\">\n\t</td>\n<td class=field_labels>&nbsp;</td></tr>\n";

				$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[519])."\n\t</td>\n\t";
				$this->body .="<td class=data_values><input type=text name=c[email] value=\"".urldecode($info[email])."\" class=data_values>";
				if (isset($this->error[email]))
					$this->body .="<font class=error_message>".urldecode($this->error[email])."</font>";
				$this->body .="</td>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_email] value=1 ";
				if ($show->EXPOSE_EMAIL)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				if ($this->registration_configuration->USE_REGISTRATION_COMPANY_NAME_FIELD)
				{
					$this->body .="<tr>\n\t<td class=field_labels>\n\t".urldecode($this->messages[520])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[company_name] value=\"".$info[company_name]."\" class=data_values>";
					if (isset($this->error[company_name]))
						$this->body .="<font class=error_message>".urldecode($this->error[company_name])."</font>";
					$this->body .="</td>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_company_name] value=1 ";
				if ($show->EXPOSE_COMPANY_NAME)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_BUSINESS_TYPE_FIELD)
				{
					$this->body .="<tr>\n\t<td class=field_labels>\n\t".urldecode($this->messages[521])."\n\t</td>\n\t";
					$this->body .="<td  class=data_values>".urldecode($this->messages[1572])."<input type=radio name=c[business_type] value=\"1\" ";
					if ($show->BUSINESS_TYPE == "1")
						$this->body .="checked";
					$this->body .="><br>".urldecode($this->messages[1571])."<input type=radio name=c[business_type] value=\"2\" ";
					if ($show->BUSINESS_TYPE == "2")
						$this->body .="checked";
					$this->body .= ">\n\t</td>\n<td class=field_labels>&nbsp;</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_FIRSTNAME_FIELD)
				{
					$this->body .="<tr>\n\t<td class=field_labels>\n\t".urldecode($this->messages[522])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[firstname] value=\"".$info[firstname]."\"class=data_values>";
					if (isset($this->error[firstname]))
						$this->body .="<font class=error_message>".urldecode($this->error[firstname])."</font>";
					$this->body .="</td>\n";
					$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_firstname] value=1 ";
					if ($show->EXPOSE_FIRSTNAME)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_LASTNAME_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[523])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[lastname] value=\"".$info[lastname]."\"class=data_values>";
					if (isset($this->error[lastname]))
						$this->body .="<font class=error_message>".urldecode($this->error[lastname])."</font>";
					$this->body .="</td>\n";
					$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_lastname] value=1 ";
					if ($show->EXPOSE_LASTNAME)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_ADDRESS_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[524])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[address] value=\"".$info[address]."\"class=data_values>";
					if (isset($this->error[address]))
						$this->body .="<font class=error_message>".urldecode($this->error[address])."</font>";
					$this->body .="</td>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_address] value=1 ";
				if ($show->EXPOSE_ADDRESS)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_ADDRESS2_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[525])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[address_2] value=\"".$info[address_2]."\"class=data_values>";
					if (isset($this->error[address_2]))
						$this->body .="<font class=error_message>".urldecode($this->error[address_2])."</font>";
					$this->body .="\n\t</td>\n<td class=field_labels>&nbsp;</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_CITY_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[526])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[city] value=\"".$info[city]."\"class=data_values>";
					if (isset($this->error[city]))
						$this->body .="<font class=error_message>".urldecode($this->error[city])."</font>";
					$this->body .="</td>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_city] value=1 ";
				if ($show->EXPOSE_CITY)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				//echo $this->registration_configuration->USE_REGISTRATION_STATE_FIELD." is use state field<Br>\n";
				if ($this->registration_configuration->USE_REGISTRATION_STATE_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[527])."\n\t</td>\n\t";
					$this->body .="<td class=data_values>\n\t";
					$this->sql_query = "SELECT * FROM ".$this->states_table." order by display_order, name";
					$result = $db->Execute($this->sql_query);
					if ($this->debug_info) echo $this->sql_query."<br>\n";
					if (!$result)
					{
						if ($this->debug_info) echo $this->sql_query."<br>\n";
						return false;
					}
					else
					{
						$this->body .="<select name=c[state] class=data_values>\n\t\t";
						$this->body .="<option value=none>".urldecode($this->messages[543])."\n\t\t";
						while ($show_state = $result->FetchNextObject()) {
							//spit out the state list
							$this->body .="<option value=\"".$show_state->ABBREVIATION."\"";
							if ($this->debug_info) echo $info[state]." - ".$show_state->ABBREVIATION."<br>\n";
							if (trim($info[state]) == trim($show_state->ABBREVIATION))
							{
								$this->body .="selected";
								if ($this->debug_info) echo  "MATCHED".$info[state]." - ".$show_state->ABBREVIATION."<br>\n";
							}


							$this->body .=">".$show_state->NAME."\n\t\t";
						}

						$this->body .="</select>\n\t";
					}
					if (isset($this->error[state]))
						$this->body .="<font class=error_message>".urldecode($this->error[state])."</font>";
					$this->body .="</td>\n";
				//echo $show->EXPOSE_STATE." is expose_state<bR>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_state] value=1 ";
				if ($show->EXPOSE_STATE)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_ZIP_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[528])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[zip] value=\"".$info[zip]."\"class=data_values>";
					if (isset($this->error[zip]))
						$this->body .="<font class=error_message>".urldecode($this->error[zip])."</font>";
					$this->body .="</td>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_zip] value=1 ";
				if ($show->EXPOSE_ZIP)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_COUNTRY_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[529])."\n\t</td>\n\t";
					$this->body .="<td class=data_values>\n\t";
					$this->sql_query = "SELECT * FROM ".$this->countries_table." order by display_order, name";
					$result = $db->Execute($this->sql_query);
					if (!$result)
						return false;
					else
					{
						$this->body .="<select name=c[country] class=data_values>\n\t\t";
						$this->body .="<option value=none>".urldecode($this->messages[544])."\n\t\t";
						while ($show_country = $result->FetchNextObject()) {
							//spit out the country list
							$this->body .="<option ";
							if ((urldecode($info[country]) == $show_country->ABBREVIATION) || (urldecode($info[country]) == $show_country->NAME))
							$this->body .="selected";
							$this->body .=">".$show_country->NAME."\n\t\t";
						}

						$this->body .="</select>\n\t";
					}
					if (isset($this->error[country]))
						$this->body .="<font class=error_message>".urldecode($this->error[country])."</font>";
					$this->body .="\n\t</td>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_country] value=1 ";
				if ($show->EXPOSE_COUNTRY)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_PHONE_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[530])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[phone] value=\"".$info[phone]."\"class=data_values>";
					if (isset($this->error[phone]))
						$this->body .="<font class=error_message>".urldecode($this->error[phone])."</font>";
					$this->body .="</td>\n";
				$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_phone] value=1 ";
				if ($show->EXPOSE_PHONE)
					$this->body .= "checked";
				$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_PHONE2_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[531])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[phone_2] value=\"".$info[phone_2]."\"class=data_values>";
					if (isset($this->error[phone_2]))
						$this->body .="<font class=error_message>".urldecode($this->error[phone_2])."</font>";
					$this->body .="</td>\n";
					$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_phone2] value=1 ";
					if ($show->EXPOSE_PHONE2)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_FAX_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[532])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[fax] value=\"".$info[fax]."\"class=data_values>";
					if (isset($this->error[fax]))
						$this->body .="<font class=error_message>".urldecode($this->error[fax])."</font>";
					$this->body .="</td>\n";
					$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_fax] value=1 ";
					if ($show->EXPOSE_FAX)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_URL_FIELD)
				{
					$this->body .="<tr>\n\t<td  class=field_labels>\n\t".urldecode($this->messages[533])."\n\t</td>\n\t";
					$this->body .="<td class=data_values><input type=text name=c[url] value=\"".$info[url]."\"class=data_values>";
					if (isset($this->error[url]))
						$this->body .="<font class=error_message>".urldecode($this->error[url])."</font>";
					$this->body .="</td>\n";
					$this->body .= "<td class=field_labels><input type=checkbox name=d[expose_url] value=1 ";
					if ($show->EXPOSE_URL)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_1_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1251]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_1"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_1] value=\"".$info["optional_field_1"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_1] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_1"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_1] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_1"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_1] value=\"".$info["optional_field_1"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_1_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_1_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_1_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_1"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_1]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_1])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_1] value=1 ";
					if ($show->EXPOSE_OPTIONAL_1)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_2_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1252]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_2"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_2] value=\"".$info["optional_field_2"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_2] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_2"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_2] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_2"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_2] value=\"".$info["optional_field_2"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_2_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_2_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_2_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_2"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_2]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_2])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_2] value=1 ";
					if ($show->EXPOSE_OPTIONAL_2)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_3_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1253]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_3"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_3] value=\"".$info["optional_field_3"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_3] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_3"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_3] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_3"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_3] value=\"".$info["optional_field_3"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_3_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_3_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_3_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_3"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_3]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_3])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_3] value=1 ";
					if ($show->EXPOSE_OPTIONAL_3)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_4_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1254]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_4"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_4] value=\"".$info["optional_field_4"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_4] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_4"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_4] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_4"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_4] value=\"".$info["optional_field_4"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_4_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_4_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_4_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_4"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_4]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_4])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_4] value=1 ";
					if ($show->EXPOSE_OPTIONAL_4)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_5_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1255]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_5"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_5] value=\"".$info["optional_field_5"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_5] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_5"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_5] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_5"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_5] value=\"".$info["optional_field_5"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_5_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_5_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_5_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_5"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_5]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_5])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_5] value=1 ";
					if ($show->EXPOSE_OPTIONAL_5)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}

				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_6_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1256]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_6"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_6] value=\"".$info["optional_field_6"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_6] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_6"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_6] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_6"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_6] value=\"".$info["optional_field_6"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_6_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_6_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_6_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_6"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_6]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_6])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_6] value=1 ";
					if ($show->EXPOSE_OPTIONAL_6)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_7_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1257]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_7"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_7] value=\"".$info["optional_field_7"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_7] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_7"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_7] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_7"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_7] value=\"".$info["optional_field_7"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_7_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_7_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_7_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_7"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_7]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_7])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_7] value=1 ";
					if ($show->EXPOSE_OPTIONAL_7)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_8_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1258]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_8"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_8] value=\"".$info["optional_field_8"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_8] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_8"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_8] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_8"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_1] value=\"".$info["optional_field_8"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_8_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_8_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_8_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_8"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_8]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_8])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_8] value=1 ";
					if ($show->EXPOSE_OPTIONAL_8)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_9_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1259]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_9"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_9] value=\"".$info["optional_field_9"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_9] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_9"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_9] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_9"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_9] value=\"".$info["optional_field_9"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_9_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_9_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_9_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_9"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_9]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_9])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td class=field_labels>&nbsp;";
					$this->body .= "<input type=checkbox name=d[expose_optional_9] value=1 ";
					if ($show->EXPOSE_OPTIONAL_9)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}
				if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_10_FIELD)
				{
					$this->body .="<tr class=field_labels>\n\t\t<td >".urldecode($this->messages[1260]);
					$this->body .="</td>\n\t\t";
					$this->body .="<td class=data_values>\n\t\t";
					if (($this->configuration_data['use_filters']) && ($this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION))
					{
						$this->body .= $info["optional_field_10"];
					}
					else
					{
						if (!$this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE)
							$this->body .= "<input type=text name=c[optional_field_10] value=\"".$info["optional_field_10"]."\" size=30 maxsize=256 class=data_values>";
						elseif($this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE == 1)
						{
							$this->body .= "<textarea name=c[optional_field_10] rows=8 cols=30 class=data_values>";
							$this->body .= $info["optional_field_10"]."</textarea>";
						}
						else
						{
							$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE;
							$type_result = $db->Execute($this->sql_query);
							if (!$type_result)
							{
								return false;
							}
							elseif ($type_result->RecordCount() > 0)
							{
								$this->body .= "<select name=c[optional_field_10] class=data_values>";
								$matched = 0;
								while ($show_dropdown = $type_result->FetchNextObject())
								{
									$this->body .= "<option value=\"".$show_dropdown->VALUE."\" ";
									if ($info["optional_field_10"] == $show_dropdown->VALUE)
									{
										$this->body .= "selected";
										$matched = 1;
									}
									$this->body .= ">".$show_dropdown->VALUE."</option>\n\t\t";
								}
								$this->body .= "</select>";
							}
							else
								//blank text box
								$this->body .= "<input type=text name=c[optional_field_10] value=\"".$info["optional_field_10"]."\"  size=30 maxsize=256 class=data_values>";
						}
						if (($this->registration_configuration->REGISTRATION_OPTIONAL_10_OTHER_BOX) && ($this->registration_configuration->REGISTRATION_OPTIONAL_10_FIELD_TYPE))
						{
							$this->body .= " ".urldecode($this->messages[1265])." <input type=text name=c[optional_field_10_other] value=\"";
							if (!$matched)
								$this->body .= $info["optional_field_10"];
							$this->body .= "\" size=15 maxsize=256 class=data_values>\n\t";
						}
					}
					if (isset($this->error[optional_field_10]))
						$this->body .="<font class=error_message>".urldecode($this->error[optional_field_10])."</font>";
					$this->body .="\n\t\t</td>\n\t";
					$this->body .="<td>&nbsp;";
					$this->body .="<input type=checkbox name=d[expose_optional_10] value=1 ";
					if ($show->EXPOSE_OPTIONAL_10)
						$this->body .= "checked";
					$this->body .= ">".urldecode($this->messages[1574])."</td></tr>\n";
				}

				//check to see if affiliate
				//display affiliate home page html form
				$this->sql_query = "select * from ".$this->user_groups_price_plans_table." where id = ".$this->classified_user_id;
				$user_group_result = $db->Execute($this->sql_query);
				if (!$user_group_result)
				{
					return false;
				}
				elseif ($user_group_result->RecordCount() == 1)
				{
					$show_user_stuff = $user_group_result->FetchNextObject();
					$this->sql_query = "select * from ".$this->groups_table." where group_id = ".$show_user_stuff->GROUP_ID;
					$group_result = $db->Execute($this->sql_query);
					if (!$group_result)
					{
						return false;
					}
					elseif ($group_result->RecordCount() == 1)
					{
						$group_stuff = $group_result->FetchNextObject();
						if (($group_stuff) && ($group_stuff->AFFILIATE))
						{
							//show link to edit the html placed on affiliate site
							$this->body .= "<tr align=center>\n\t<td class=field_labels colspan=3>\n\t".urldecode($this->messages[1364])."</font>\n\t</td>\n\t</tr>\n\t";
							$this->body .= "<tr><td colspan=3><table width=100%>";
							$this->body .= "<tr>\n\t<td class=data_values colspan=2>\n\t<textarea cols=75 rows=15 name=c[affiliate_html] style=\"width:100%\">";
							if(strlen($info["affiliate_html"]) == 0)
							{
								$info["affiliate_html"] = '<table width="100%" border="0" cellspacing="0" cellpadding="5" bgcolor="#eeeeee"><tr><td align="center"><font face="Arial, Helvetica, sans-serif" size="3"><b>Welcome to your Affiliate Page</b></td></tr><tr><td><font face="Arial, Helvetica, sans-serif" size="2">This is your personal Affiliate Page. You can modify this shaded area with any html you wish by accessing your Account, clicking [my current information], then [edit my info] and entering your html into the Affiliate Page HTML text area. Use this page to display a link back to your own website, list additional products, etc. This page will also display a table of your active ads currently listed on this site. Since you have a unique Affiliate Page URL (found on your Personal Information Page) you can email this link to friends and family or post this link on other websites, etc. When visitors click on your Affiliate Page Link they will be sent directly to this Affiliate Page.<br><br> <b>NOTE:</b> Remember that any images or links you place within this html will need to be "absolute" linked to their appropriate location on your own server. In other words, all links must contain the "full URL" (i.e. http://www.yourwebsite.com/images/yourimage.gif). "Relative" links will not work within this html (i.e. images/yourimage.gif).</font></td></tr></table>';
							}
							$this->body .= $info["affiliate_html"]."</textarea>\n\t</td>\n</tr>\n";
							$this->body .= "</table></td></tr>\n\t";
						}
					}
				}

				$this->body .="<tr >\n\t<td colspan=3 align=center>\n\t"."<input type=submit value=\"".urldecode($this->messages[534])."\" class=submit_changes_button>\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td align=center colspan=3 class=edit_your_info_link>
					<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=3 class=submit_changes_button>".urldecode($this->messages[551])."</a>
					\n\t</td>\n</tr>\n";
				$this->body .="<tr>\n\t<td align=center colspan=3 class=edit_your_info_link>
					<a href=".$this->configuration_data['classifieds_file_name']."?a=4 class=submit_changes_button>".urldecode($this->messages[552])."</a>
					\n\t</td>\n</tr>\n";
				$this->body .="</table>\n";
				$this->body .="</form>\n";
				$this->display_page($db);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}

	} //end of function edit_user_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


	function update_user($db,$info=0,$expose_info=0)
	{
		if (($info) && ($this->classified_user_id))
		{
			$this->sql_query = "select * from ".$this->userdata_table." where id = ".$this->classified_user_id;
			$result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<Br>\n";
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				$show = $result->FetchNextObject();
				$this->sql_query = "insert into ".$this->userdata_history_table."
					(date_of_change,id,username,email,company_name,business_type,firstname,lastname,
					address,address_2,zip,city,state,country,phone,phone2,fax,url,optional_field_1,
					optional_field_2,optional_field_3,optional_field_4,optional_field_5,optional_field_6,optional_field_7,
					optional_field_8,optional_field_9,optional_field_10)
					values
					(".$this->shifted_time($db).",
					".$show->ID.",
					\"".$show->USERNAME."\",
					\"".$show->EMAIL."\",
					\"".addslashes($show->COMPANY_NAME)."\",
					\"".$show->BUSINESS_TYPE."\",
					\"".addslashes($show->FIRSTNAME)."\",
					\"".addslashes($show->LASTNAME)."\",
					\"".addslashes($show->ADDRESS)."\",
					\"".addslashes($show->ADDRESS_2)."\",
					\"".addslashes($show->ZIP)."\",
					\"".addslashes($show->CITY)."\",
					\"".$show->STATE."\",
					\"".$show->COUNTRY."\",
					\"".addslashes($show->PHONE)."\",
					\"".addslashes($show->PHONE2)."\",
					\"".addslashes($show->FAX)."\",
					\"".addslashes($show->URL)."\",
					\"".addslashes($show->OPTIONAL_FIELD_1)."\",
					\"".addslashes($show->OPTIONAL_FIELD_2)."\",
					\"".addslashes($show->OPTIONAL_FIELD_3)."\",
					\"".addslashes($show->OPTIONAL_FIELD_4)."\",
					\"".addslashes($show->OPTIONAL_FIELD_5)."\",
					\"".addslashes($show->OPTIONAL_FIELD_6)."\",
					\"".addslashes($show->OPTIONAL_FIELD_7)."\",
					\"".addslashes($show->OPTIONAL_FIELD_8)."\",
					\"".addslashes($show->OPTIONAL_FIELD_9)."\",
					\"".addslashes($show->OPTIONAL_FIELD_10)."\")";
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<Br>\n";
				if (!$result)
				{
					return false;
				}

				//needs error checking
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_1_filter_association']))
					$info["optional_field_1"] = $show->OPTIONAL_FIELD_1;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_2_filter_association']))
					$info["optional_field_2"] = $show->OPTIONAL_FIELD_2;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_3_filter_association']))
					$info["optional_field_3"] = $show->OPTIONAL_FIELD_3;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_4_filter_association']))
					$info["optional_field_4"] = $show->OPTIONAL_FIELD_4;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_5_filter_association']))
					$info["optional_field_5"] = $show->OPTIONAL_FIELD_5;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_6_filter_association']))
					$info["optional_field_6"] = $show->OPTIONAL_FIELD_6;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_7_filter_association']))
					$info["optional_field_7"] = $show->OPTIONAL_FIELD_7;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_8_filter_association']))
					$info["optional_field_8"] = $show->OPTIONAL_FIELD_8;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_9_filter_association']))
					$info["optional_field_9"] = $show->OPTIONAL_FIELD_9;
				if (($this->configuration_data['use_filters']) && ($this->configuration_data['optional_10_filter_association']))
					$info["optional_field_10"] = $show->OPTIONAL_FIELD_10;
				$this->sql_query = "update ".$this->userdata_table." set
					email = \"".$info["email"]."\",
					company_name = \"".addslashes($info["company_name"])."\",
					business_type = \"".$info["business_type"]."\",
					firstname = \"".addslashes($info["firstname"])."\",
					lastname = \"".addslashes($info["lastname"])."\",
					address = \"".addslashes($info["address"])."\",
					address_2 = \"".addslashes($info["address_2"])."\",
					city = \"".addslashes($info["city"])."\",
					state = \"".$info["state"]."\",
					country = \"".addslashes($info["country"])."\",
					zip = \"".addslashes($info["zip"])."\",
					phone = \"".addslashes($info["phone"])."\",
					phone2 = \"".addslashes($info["phone_2"])."\",
					fax = \"".addslashes($info["fax"])."\",
					url = \"".addslashes($info["url"])."\",
					affiliate_html = \"".addslashes($info["affiliate_html"])."\",
					optional_field_1 = \"".addslashes($info["optional_field_1"])."\",
					optional_field_2 = \"".addslashes($info["optional_field_2"])."\",
					optional_field_3 = \"".addslashes($info["optional_field_3"])."\",
					optional_field_4 = \"".addslashes($info["optional_field_4"])."\",
					optional_field_5 = \"".addslashes($info["optional_field_5"])."\",
					optional_field_6 = \"".addslashes($info["optional_field_6"])."\",
					optional_field_7 = \"".addslashes($info["optional_field_7"])."\",
					optional_field_8 = \"".addslashes($info["optional_field_8"])."\",
					optional_field_9 = \"".addslashes($info["optional_field_9"])."\",
					optional_field_10 = \"".addslashes($info["optional_field_10"])."\"
					where id =".$this->classified_user_id;
				$result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<Br>\n";
				if (!$result)
				{
					echo $this->sql_query." is the query<br>\n";
					return false;
				}
				else
				{
					//echo $this->sql_query." - 1<Br>\n";
					$this->sql_query = "update ".$this->logins_table." set
						password = \"".$info["password"]."\"
						where id =".$this->classified_user_id;
					$result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<Br>\n";
					if (!$result)
					{
						//$this->body .=$this->sql_query." is the query<br>\n";
						return false;
					}
					else
					{
						//reset and update whether to expose personal data
						$this->sql_query = "update ".$this->userdata_table." set
							expose_email = 0,
							expose_company_name = 0,
							expose_firstname = 0,
							expose_lastname = 0,
							expose_address = 0,
							expose_city = 0,
							expose_state = 0,
							expose_country = 0,
							expose_zip = 0,
							expose_phone = 0,
							expose_phone2 = 0,
							expose_fax = 0,
							expose_url = 0,
							expose_optional_1 = 0,
							expose_optional_2 = 0,
							expose_optional_3 = 0,
							expose_optional_4 = 0,
							expose_optional_5 = 0,
							expose_optional_6 = 0,
							expose_optional_7 = 0,
							expose_optional_8 = 0,
							expose_optional_9 = 0,
							expose_optional_10 = 0
							where id =".$this->classified_user_id;
						$result = $db->Execute($this->sql_query);
						//echo $this->sql_query." is the query<br>\n";
						if (!$result)
						{
							//echo $this->sql_query." is the query<br>\n";
							return false;
						}
						else
						{
							//echo $expose_info." is expose_info<Br>\n";
							if (is_array($expose_info))
							{
								foreach ($expose_info as $key => $value)
								{
									if ($value == 1)
									{
										$this->sql_query = "update ".$this->userdata_table." set ".
											$key." = 1
											where id =".$this->classified_user_id;
										$result = $db->Execute($this->sql_query);
										//echo $this->sql_query." is the query<br>\n";
										if (!$result)
										{
											echo $this->sql_query." is the query<br>\n";
											return false;
										}
									}
								}
							}
							if ($this->configuration_data['use_api'])
							{
								//echo "hello from use api in update user<br>\n";
								$info["username"] = $show->USERNAME;
								include("config.php");
								$info["db_host"] = $db_host;
								$info["db_name"] = $database;
								$info["installation_type"] = 1;
								include_once("classes/api_update_user.php");
								$update_user = new API_update_user($info);
								$update_user->api_update_user_info();
							}

							//this will send an email to the admin notifying them of changes to a
							//users information
							//if ($this->configuration_data->ADMIN_EMAIL_EDIT)
							if (0)
							{
								$subject = "User details have been edited for ".$show->USERNAME;
								$message = "Below is the new user information:\n\n";
								$message .= "email = ".$info["email"]."\n";
								$message .= "company_name = ".$info["company_name"]."\n";
								$message .= "business_type = ".$info["business_type"]."\n";
								$message .= "firstname = ".$info["firstname"]."\n";
								$message .= "lastname = ".$info["lastname"]."\n";
								$message .= "address = ".$info["address"]."\n";
								$message .= "address_2 = ".$info["address_2"]."\n";
								$message .= "city = ".$info["city"]."\n";
								$message .= "state = ".$info["state"]."\n";
								$message .= "country = ".$info["country"]."\n";
								$message .= "zip = ".$info["zip"]."\n";
								$message .= "phone = ".$info["phone"]."\n";
								$message .= "phone2 = ".$info["phone_2"]."\n";
								$message .= "fax = ".$info["fax"]."\n";
								$message .= "url = ".$info["url"]."\n";
								$message .= "affiliate_html = ".$info["affiliate_html"]."\n";
								$message .= "optional_field_1 = ".$info["optional_field_1"]."\n";
								$message .= "optional_field_2 = ".$info["optional_field_2"]."\n";
								$message .= "optional_field_3 = ".$info["optional_field_3"]."\n";
								$message .= "optional_field_4 = ".$info["optional_field_4"]."\n";
								$message .= "optional_field_5 = ".$info["optional_field_5"]."\n";
								$message .= "optional_field_6 = ".$info["optional_field_6"]."\n";
								$message .= "optional_field_7 = ".$info["optional_field_7"]."\n";
								$message .= "optional_field_8 = ".$info["optional_field_8"]."\n";
								$message .= "optional_field_9 = ".$info["optional_field_9"]."\n";
								$message .= "optional_field_10 = ".$info["optional_field_10"]."\n";

								if ($this->configuration_data->EMAIL_CONFIGURATION_TYPE == 1)
									$message = str_replace("\n\n","\n",$message);

								if ($this->configuration_data->EMAIL_HEADER_BREAK)
									$separator = "\n";
								else
									$separator = "\r\n";
								$from = "From: ".$this->configuration_data->SITE_EMAIL.$separator."Reply-to: ".$this->configuration_data->SITE_EMAIL.$separator;

								$additional = "-f".$this->configuration_data->SITE_EMAIL;

								if ($this->configuration_data->EMAIL_CONFIGURATION == 1)
									mail($this->configuration_data->SITE_EMAIL,$subject,$message,$from,$additional);
								elseif ($this->configuration_data->EMAIL_CONFIGURATION == 2)
									mail($this->configuration_data->SITE_EMAIL,$subject,$message,$from);
								else
									mail($this->configuration_data->SITE_EMAIL,$subject,$message);
							}

							return true;
						}
					}
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

	} //end of function update_user

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_footer($db)
	{
		$this->body .="</td>\n</tr>\n";
		$this->body .="</table>\n";

	} //end of function user_management_footer

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function user_management_header($db,$switch=0)
	{

		$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr>\n\t<td width=100% valign=top>\n\t";

	} //end of function user_management_header

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_number_of_user_credits($db)
	{
		//expire user credits
		$this->sql_query = "select credit_count from ".$this->user_credits_table." where user_id = ".$this->classified_user_id." order by credits_expire asc limit 1";
		$credits_results = $db->Execute($this->sql_query);
		//echo $this->sql_query."<bR>\n";
		if (!$credits_results)
		{
			return false;
		}
		elseif ($credits_results->RecordCount() == 1)
		{
			$show_credits = $credits_results->FetchNextObject();
			return $show_credits->CREDIT_COUNT;
		}
		elseif ($credits_results->RecordCount() == 0)
		{
			return 0;
		}
		else
		{
			return false;
		}
	} //end of function get_user_credits

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_user_subscription($db)
	{
		$this->sql_query = "select * from ".$this->user_subscriptions_table." where subscription_expire > ".$this->shifted_time($db)." and user_id = ".$this->classified_user_id;
		$get_subscriptions_results = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";

		if (!$get_subscriptions_results)
		{
			return false;
		}
		elseif ($get_subscriptions_results->RecordCount() == 1)
		{
			$show_subscription = $get_subscriptions_results->FetchNextObject();
			return $show_subscription;
		}
		else
		{

		}
	} // end of function check_user_subscription

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_registration_configuration_data($db)
	{
		$this->sql_query = "SELECT * FROM ".$this->registration_configuration_table;
		//echo $this->sql_query."<BR>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			return false;
		}
		else
		{
			$this->registration_configuration = $result->FetchNextObject();
		}
		return true;
	} //end of function get_registration_configuration_data

//########################################################################

	function check_info($db,$info=0)
	{
		$this->page_id = 38;
		$this->get_text($db);
		$this->error = array();
		$this->error_found = 0;

		if ($this->registration_configuration->USE_REGISTRATION_COMPANY_NAME_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_COMPANY_NAME_FIELD)
			{
				if (strlen(trim($info[company_name])) == 0) {
					$this->error[company_name] =urldecode($this->messages[535]);
					$this->error_found++;
				}
			}
		}
		if ($this->registration_configuration->USE_REGISTRATION_FIRSTNAME_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_FIRSTNAME_FIELD)
			{
				if (strlen(trim($info[firstname])) == 0) {
					$this->error[firstname] =urldecode($this->messages[536]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_LASTNAME_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_LASTNAME_FIELD)
			{
				if (strlen(trim($info[lastname])) == 0 ) {
					$this->error[lastname] =urldecode($this->messages[537]);
					$this->error_found++;
		  		}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_ADDRESS_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_ADDRESS_FIELD)
			{
				if (strlen(trim($info[address]))== 0 ) {
					$this->error[address] =urldecode($this->messages[538]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_ADDRESS2_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_ADDRESS2_FIELD)
			{
				if (strlen(trim($info[address_2]))== 0 ) {
					$this->error[address_2] =urldecode($this->messages[538]);
					$this->error_found++;
				}
			}
		}

		if (strlen(trim($info[email])) > 0)
		{
			if (eregi("^([a-z0-9]+)(([a-z0-9._-]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2,3}([a-z0-9])?$", $info[email]))
			{
				$this->sql_query = "select id from ".$this->userdata_table." where email = \"".$info[email]."\"";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//$this->body .=$this->sql_query." is the id check query<br>\n";
					$this->error["registration"] =urldecode($this->messages[230]);
					return false;
				}
				elseif ($result->RecordCount == 1)
				{
					$show_id = $result->FetchNextObject();
					if ($show_id->ID != $this->classified_user_id)
					{
						$this->error[email] =urldecode($this->messages[539]);
						$this->error_found++;
					}
				}
				elseif ($result->RecordCount() > 1)
				{
					//email already in use
					//is it this user?
					$this->error[email] =urldecode($this->messages[539]);
					$this->error_found++;
				}

			}
	  		else
	  		{
				$this->error[email] =urldecode($this->messages[540]);
				$this->error_found++;
	  		}
		}
		else
		{
			$this->error[email] =urldecode($this->messages[541]);
			$this->error_found++;
		}
		//$this->error[email] = "does not check now - remove before release";

		if ($this->registration_configuration->USE_REGISTRATION_CITY_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_CITY_FIELD)
			{
				if (strlen(trim($info[city])) == 0 )
				{
					$this->error[city] =urldecode($this->messages[542]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_STATE_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_STATE_FIELD)
			{
				if ($info[state] == "none" )
				{
					$this->error[state] =urldecode($this->messages[543]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_COUNTRY_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_COUNTRY_FIELD)
			{
				if ($info[country] == "none" )
				{
					$this->error[country] =urldecode($this->messages[544]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_ZIP_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_ZIP_FIELD)
			{
				if (strlen(trim($info[zip])) == 0 )
				{
					$this->error[zip] =urldecode($this->messages[545]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_PHONE_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_PHONE_FIELD)
			{
				if (strlen(trim($info[phone])) == 0 )
				{
					$this->error[phone] =urldecode($this->messages[546]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_PHONE2_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_PHONE2_FIELD)
			{
				if (strlen(trim($info[phone_2])) == 0 )
				{
					$this->error[phone_2] =urldecode($this->messages[548]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_FAX_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_FAX_FIELD)
			{
				if (strlen(trim($info[fax])) == 0 )
				{
					$this->error[fax] =urldecode($this->messages[547]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_URL_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_URL_FIELD)
			{
				if (strlen(trim($info[url])) == 0 )
				{
					$this->error[url] =urldecode($this->messages[549]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_1_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_1_FIELD)
			{
				if (strlen(trim($info[optional_field_1])) == 0 )
				{
					$this->error[optional_field_1] =urldecode($this->messages[1266]);
					$this->error_found++;
				}
			}
		}

					if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_2_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_2_FIELD)
			{
				if (strlen(trim($info[optional_field_2])) == 0 )
				{
					$this->error[optional_field_2] =urldecode($this->messages[1267]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_3_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_3_FIELD)
			{
				if (strlen(trim($info[optional_field_3])) == 0 )
				{
					$this->error[optional_field_3] =urldecode($this->messages[1268]);
					$this->error_found++;
				}
			}
		}

					if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_4_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_4_FIELD)
			{
				if (strlen(trim($info[optional_field_4])) == 0 )
				{
					$this->error[optional_field_4] =urldecode($this->messages[1269]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_5_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_5_FIELD)
			{
				if (strlen(trim($info[optional_field_5])) == 0 )
				{
					$this->error[optional_field_5] =urldecode($this->messages[1270]);
					$this->error_found++;
				}
			}
		}

					if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_6_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_6_FIELD)
			{
				if (strlen(trim($info[optional_field_6])) == 0 )
				{
					$this->error[optional_field_6] =urldecode($this->messages[1271]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_7_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_7_FIELD)
			{
				if (strlen(trim($info[optional_field_7])) == 0 )
				{
					$this->error[optional_field_7] =urldecode($this->messages[1272]);
					$this->error_found++;
				}
			}
		}

					if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_8_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_8_FIELD)
			{
				if (strlen(trim($info[optional_field_8])) == 0 )
				{
					$this->error[optional_field_8] =urldecode($this->messages[1273]);
					$this->error_found++;
				}
			}
		}

		if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_9_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_9_FIELD)
			{
				if (strlen(trim($info[optional_field_9])) == 0 )
				{
					$this->error[optional_field_9] =urldecode($this->messages[1274]);
					$this->error_found++;
				}
			}
		}

					if ($this->registration_configuration->USE_REGISTRATION_OPTIONAL_10_FIELD)
		{
			if ($this->registration_configuration->REQUIRE_REGISTRATION_OPTIONAL_10_FIELD)
			{
				if (strlen(trim($info[optional_field_10])) == 0 )
				{
					$this->error[optional_field_10] =urldecode($this->messages[1275]);
					$this->error_found++;
				}
			}
		}

		if ($info["password"] == $info["password_verify"])
		{
			//$this->body .="passwords match<Br>\n";
			$password = trim($info["password"]);
			$password_length = strlen($password);
			if ((($password_length >12) || ($password_length < 5)) || (!ereg("^[[:alnum:]_-]+$", $password)))
			{
					$this->error["password"] =urldecode($this->messages[550]);
					$this->error_found++;
			}
		}
		else
		{
			$this->error[password] =urldecode($this->messages[550]);
			$this->error_found++;
		}

		//echo $this->error_found." is the error count<br>\n";
		//reset($this->error);
		//foreach ($this->error as $key => $value)
		//	echo $key." is the key to ".$value."<br>\n";

		if ($this->error_found > 0)
			return false;
		else
			return true;

	} //end of function check_info($info)

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function filter_select($db,$filter_id=0)
	{
		//check current temp filter
		//see if there are subfilters
		if ($this->configuration_data['use_filters'])
		{
			if ($filter_id)
			{
				$this->sql_query = "select ".$this->filters_table.".filter_id, ".$this->filters_table.".filter_level, ".$this->filters_table.".parent_id,".$this->filters_languages_table.".filter_name
					from ".$this->filters_table.",".$this->filters_languages_table."
					where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
					and ".$this->filters_languages_table.".language_id = ".$this->language_id."
					and ".$this->filters_table.".parent_id = ".$filter_id." order by display_order asc";
			}
			else
				$this->sql_query = "select ".$this->filters_table.".filter_id, ".$this->filters_table.".filter_level, ".$this->filters_table.".parent_id,".$this->filters_languages_table.".filter_name
					from ".$this->filters_table.",".$this->filters_languages_table."
					where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
					and ".$this->filters_languages_table.".language_id = ".$this->language_id."
					and filter_level = 1 order by display_order asc";
			$sub_filter_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$sub_filter_result)
			{
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($sub_filter_result->RecordCount() > 0)
			{
				$this->page_id = 93;
				$this->get_text($db);
				//display the form top
				$this->body .="<form action=".$this->configuration_data['classifieds_file_name']."?a=4&b=14 method=post>\n";
				$this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% align=center>\n";
				$this->body .="<tr class=user_management_page_title><td valign=top colspan=2 width=100%>\n\t\t".urldecode($this->messages[1506])."\n\t\t</td>\n\t</tr>\n\t";
				$this->body .="<tr class=page_title>\n\t<td colspan=2 >\n\t".urldecode($this->messages[1507])."\n\t</td>\n</tr>\n";
				$this->body .="<tr class=page_description  >\n\t<td colspan=2>\n\t".urldecode($this->messages[1508])."\n\t</td>\n</tr>\n";

				$this->body .="<tr class=filter_selection>\n\t\t<td>";
				//get the parent filters to this one
				$show_level = $sub_filter_result->FetchNextObject();
				if ($show_level->PARENT_ID != 0)
				{
					//show the parent levels
					$filter_tree = $this->get_filter_level($db,$show_level->PARENT_ID);
					$this->filter_level_array = array_reverse($this->filter_level_array);
					reset ($this->filter_level_array);
					if ($filter_tree)
					{
						foreach ($this->filter_level_array as $key => $value)
							$this->body .= $this->filter_level_array[$key]["filter_name"]." > ";
					}
				}
				$sub_filter_result->Move(0);

				//show the form to select filter
				$this->body .= "<select name=user_management_filter_id class=filter_dropdown onChange=\"if(this.options[this.selectedIndex].value != '') this.form.submit();\">\n\t\t";
				$this->body .= "<option value=\"\">".urldecode($this->messages[1509])."</option>\n\t\t";
				while ($show_filter = $sub_filter_result->FetchNextObject())
				{
					$this->body .= "<option value=".$show_filter->FILTER_ID.">".$show_filter->FILTER_NAME."</option>\n\t\t";
				}
				$this->body .= "</select>";
				//display the form bottom
				$this->body .= "</td></tr>";
				$this->body .="<tr>\n\t<td align=center colspan=2 class=link_back_to_user_information>
					<a href=".$this->configuration_data['classifieds_file_name']."?a=4&b=4>".urldecode($this->messages[1510])."</a>
					\n\t</td>\n</tr>\n";
				$this->body .= "</table></form>";
				$this->display_page($db);
				exit;
			}
			else
			{
				//update current ads with the current filter
				//update current userdata
				//this is the terminal filter...set it
				$this->sql_query = "update ".$this->userdata_table." set
					filter_id = ".$filter_id."
					where id = ".$this->classified_user_id;
				$filter_id_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$filter_id_result)
				{
					return false;
				}
				else
				{
					$this->sql_query = "update ".$this->classifieds_table." set
						filter_id = ".$filter_id."
						where seller = ".$this->classified_user_id;
					$classifieds_filter_id_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$classifieds_filter_id_result)
					{
						return false;
					}

					//update the new values within this user current personal information
					if ($this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_1_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_1 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_2_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_2 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_3_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_3 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_4_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_4 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_5_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_5 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_6_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_6 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_7_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_7 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_8_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_8 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_9_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_9 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					if ($this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION)
					{
						$filter_value = $this->get_filter_value($db,$this->registration_configuration->REGISTRATION_OPTIONAL_10_FILTER_ASSOCIATION,$filter_id);
						$this->sql_query = "update ".$this->userdata_table." set
							optional_field_10 = \"".$filter_value."\"
							where id = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}

					//update the new values within this users current classified ads
					if ($this->configuration_data['optional_1_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_1_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_1 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_2_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_2_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_2 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_3_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_3_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_3 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_4_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_4_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_4 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_5_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_5_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_5 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_6_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_6_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_6 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_7_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_7_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_7 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_8_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_8_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_8 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_9_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_9_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_9 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					if ($this->configuration_data['optional_10_filter_association'])
					{
						$filter_value = $this->get_filter_value($db,$this->configuration_data['optional_10_filter_association'],$filter_id);
						$this->sql_query = "update ".$this->classifieds_table." set
							optional_field_10 = \"".$filter_value."\"
							where seller = ".$this->classified_user_id;
						$update_result = $db->Execute($this->sql_query);
						if (!$update_result)
							return false;
					}
					return true;
				}
			}
		}
		else
			return false;

	} //end of function filter_select

//########################################################################

	function get_filter_value($db,$association=0,$filter_id=0)
	{
		if (($association) && ($filter_id))
		{
			//association is the filter level this value is associated with
			$this->sql_query = "select count(distinct(filter_level)) as level_count from ".$this->filters_table;
			$level_count_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$level_count_result)
			{
				$this->error_message = $this->messages[5501];
				return false;
			}
			elseif ($level_count_result->RecordCount() == 1)
			{
				$level_count = $level_count_result->FetchNextObject();

				if ($level_count->LEVEL_COUNT == $association)
				{
					//get current filter id filter name
					$this->sql_query = "select ".$this->filters_languages_table.".filter_name
						from ".$this->filters_languages_table."
						where ".$this->filters_languages_table.".language_id = ".$this->language_id."
						and ".$this->filters_languages_table.".filter_id = ".$filter_id;
					$filter_result =  $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$filter_result)
					{
						//echo $this->sql_query." is the query<br>\n";
						$this->error_message = $this->messages[3501];
						return false;
					}
					elseif ($filter_result->RecordCount() == 1)
					{
						$show_filter_name = $filter_result->FetchNextObject();
						return $show_filter_name->FILTER_NAME;
					}
					else
						return false;

				}
				else
				{
					$filter_name = $this->get_filter_level($db,$filter_id,$association);
					return $filter_name;
				}
			}
			else
			{
				return false;
			}
		}
		else
			return false;
	} //end of function get_filter_value

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_filter_level($db,$filter=0,$level_result=0)
	{
		if ($filter)
		{
			$i = 0;
			$filter_next = $filter;
			do
			{
				$this->sql_query = "select ".$this->filters_table.".filter_id,".$this->filters_table.".parent_id,
					".$this->filters_languages_table.".filter_name, ".$this->filters_table.".filter_level
					from ".$this->filters_table.",".$this->filters_languages_table."
					where ".$this->filters_table.".filter_id = ".$this->filters_languages_table.".filter_id
					and ".$this->filters_languages_table.".language_id = ".$this->language_id."
					and ".$this->filters_table.".filter_id = ".$filter_next;
				$filter_result =  $db->Execute($this->sql_query);
				//echo $this->sql_query." is the query<br>\n";
				if (!$filter_result)
				{
					//echo $this->sql_query." is the query<br>\n";
					$this->error_message = $this->messages[3501];
					return false;
				}
				elseif ($filter_result->RecordCount() == 1)
				{
					$show_filter = $filter_result->FetchNextObject();
					$this->filter_level_array[$i]["parent_id"]  = $show_filter->PARENT_ID;
					$this->filter_level_array[$i]["filter_name"] = $show_filter->FILTER_NAME;
					$this->filter_level_array[$i]["filter_id"]   = $show_filter->FILTER_ID;
					$this->filter_level_array[$i]["filter_level"]   = $show_filter->FILTER_LEVEL;
					if (($level_result) && ($level_result == $show_filter->FILTER_LEVEL))
						return $show_filter->FILTER_NAME;
					$i++;
					$filter_next = $show_filter->PARENT_ID;
				}
				else
				{
					//echo "wrong return<Br>\n";
					return false;
				}

			} while ( $show_filter->PARENT_ID != 0 );

			return $i;
		}
		else
			return false;

	} // end of function get_filter_level

//########################################################################

} // end

?>
