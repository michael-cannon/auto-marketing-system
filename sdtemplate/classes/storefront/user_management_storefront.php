<?php
class User_management_storefront extends Store
{
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function User_management_storefront ($db,$language_id,$classified_user_id=0, $product_configuration=0,$storefront_id=0)
	{
		$this->Store($db,6,$language_id,$classified_user_id, $product_configuration,$storefront_id);
	} //end of function User_management_storefront
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function addCategory($db, $categoryName)
	{
		if(!$categoryName){ $this->storefrontManagementError .= 'Missing category name.  '; return false; }
		$this->sql_query="insert into ".$this->storefront_categories_table."
		(user_id, category_name) 
		values 
		('".$this->storefront_id."','".addslashes(htmlspecialchars($categoryName))."')
		";
		if($db->Execute($this->sql_query))
		{
			if($this->ajax)
				echo "categories|".htmlspecialchars($categoryName)."|categoryId_".$db->Insert_ID()."|LI";
		}else{
			if($this->ajax)
				echo "error|Category couldn't be added";
			else
				return false;
		}
		if($this->ajax)
			exit;
		else
			return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function editCategory($db, $categoryId, $categoryName)
	{
		if(!$categoryName){ $this->storefrontManagementError .= 'Missing category name.  '; return false; }
		if(!$categoryId){ $this->storefrontManagementError .= 'Missing category information.  '; return false; }
		if(!$this->isStorefrontCategory($db, $categoryId)){ $this->storefrontManagementError .= 'This category does not belong to you  '; return false; }
		$this->sql_query = "update ".$this->storefront_categories_table." set
		category_name = '".addslashes(htmlspecialchars($categoryName))."'
		where category_id = '$categoryId'";
		if(!$db->Execute($this->sql_query))
		{
			return false;
		}
		return true;
			
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function deleteCategory($db, $categoryId1, $categoryId2)
	{
		if(!$categoryId1){ $this->storefrontManagementError .= 'Missing category information.  '; return false; }
		if(!$categoryId2){ $this->storefrontManagementError .= 'Missing category information.  '; return false; }
		if(!$this->isStorefrontCategory($db, $categoryId1)){ $this->storefrontManagementError .= 'This category does not belong to you.  '; return false; }
		if(!$this->isStorefrontCategory($db, $categoryId2)){ $this->storefrontManagementError .= 'This category does not belong to you.  '; return false; }
		if($categoryId1==$categoryId2){ $this->storefrontManagementError .= 'You cannot shift items into a category that is about to be deleted.  '; return false; }
		$this->sql_query = "update ".$this->classifieds_table." set
		storefront_category = '$categoryId2'
		where storefront_category = '$categoryId1'";
		if(!$db->Execute($this->sql_query))
		{
			return false;
		}
		$this->sql_query = "delete from ".$this->storefront_categories_table." where category_id = '$categoryId1'";
		if(!$db->Execute($this->sql_query))
		{
			return false;
		}
		return true;
			
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function sortCategory($db, $categoryOrders)
	{
		if(!$categoryOrders){ $this->storefrontManagementError .= 'Missing category information.  '; return false; }
		foreach($categoryOrders as $categoryId => $categoryOrder)
		{
			if(!$this->isStorefrontCategory($db, $categoryId)){ $this->storefrontManagementError .= 'This category does not belong to you  '; return false; }
			$this->sql_query = "update ".$this->storefront_categories_table." set 
			display_order = '".$categoryOrder."' 
			where category_id = '$categoryId'";
			if(!$db->Execute($this->sql_query))
			{
				return false;
			}
		}
		return true;
			
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function switchTemplate($db, $templateId)
	{
		if(!$templateId){ $this->storefrontManagementError .= 'Missing template information.  '; return false; }
		$this->sql_query = "update ".$this->userdata_table." set 
		storefront_template_id = '".$templateId."' 
		where id = '$this->storefront_id'";
		if(!$db->Execute($this->sql_query))
		{
			return false;
		}
		return true;
			
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function addHeaderImage($db, $fileInfo)
	{
		$this->getStorefrontImageDir($db);
		
		if($this->storefrontUserData["storefront_header"])
		{
			$oldStorefrontLogo = $this->storefrontImageDir.$this->storefrontUserData["storefront_header"];
			unlink($oldStorefrontLogo);
		}
		
		if($fileInfo&&$fileInfo["size"]>0)
		{
			$mimeType = $fileInfo["type"];
			$this->get_image_file_types_array($db);
			if(!$this->image_accepted_type($mimeType))
			{
				$this->storefrontManagementError .= 'This image type is not allowed.  ';
				return false;
			} 
			$newStorefrontLogo = md5($this->storefront_id.time());
			copy($fileInfo["tmp_name"],$this->storefrontImageDir.$newStorefrontLogo);
		}
		
		$this->sql_query="update ".$this->userdata_table." set 
		storefront_header =
		'".$newStorefrontLogo."' 
		where id = '".$this->storefront_id."'
		";
		if(!$db->Execute($this->sql_query))
			return false;
				
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function addWelcomeNote($db, $welcomeInfo)
	{
		$welcomeInfo = strip_tags($welcomeInfo);
		$this->sql_query="update ".$this->userdata_table." set 
		storefront_welcome_message =
		'".$welcomeInfo."' 
		where id = '".addslashes($this->storefront_id)."'
		";
		if(!$db->Execute($this->sql_query))
			return false;
				
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function toggleStorefrontActivity($db)
	{
		$storefrontHoldSetting = ($this->storefrontUserData["storefront_on_hold"]==1) ? 0 : 1;
		$this->sql_query="update ".$this->userdata_table." set 
		storefront_on_hold = 
		'".$storefrontHoldSetting."' 
		where id = '".$this->storefront_id."'
		";
		if(!$db->Execute($this->sql_query))
			return false;
				
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function addPage($db, $pageName, $pageLinkText=0)
	{
		if(!$pageName){ $this->storefrontManagementError .= 'Missing page name.  '; return false; }
		$pageLinkText = $pageLinkText ? $pageLinkText : $pageName;
		$this->sql_query="insert into ".$this->storefront_pages_table." 
		(user_id, page_name, page_link_text) 
		values 
		('".$this->storefront_id."','".addslashes(htmlspecialchars($pageName))."','".addslashes(htmlspecialchars($pageLinkText))."')
		";
		if(!$db->Execute($this->sql_query))
			return false;
			
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function editPage($db, $pageInfo, $pageSetting)
	{
		if(!$pageInfo){ $this->storefrontManagementError .= 'Missing page information.  '; return false; }
		$this->sql_query = "update ".$this->storefront_pages_table." set 
		page_id = '".$this->pageId."', 
	 	$pageSetting = '".urlencode($this->replace_disallowed_html($db,$pageInfo))."' 
	 	where user_id = '".$this->storefront_id."'
		";
		if(!$db->Execute($this->sql_query))
			return false;
			
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function deletePage($db)
	{
		$this->sql_query = "delete from ".$this->storefront_pages_table."
		where 
		user_id = '".$this->storefront_id."' and 
		page_id = '".$this->pageId."'";
		if(!$db->Execute($this->sql_query))
			return false;
			
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function display_subscription_info($db, $user_id)
	{
		$this->subscription_info .= "<tr>\n\t<td class=my_current_info_title colspan=2>".urldecode($this->messages[500000])."</font>\n\t</td>\n</tr>";
		$this->subscription_info .= "<tr>\n\t<td class=page_description colspan=2>".urldecode($this->messages[500001])."</font>\n\t</td>\n</tr>";
		
		$this->subscription_info .= "<tr>\n\t<td colspan=2>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>";
		
		//charge by subscription -- display when expire
		$this->sql_query = "select * from ".Store::get('storefront_subscriptions_table')." where user_id = $user_id";
		$subscription_results = $db->Execute($this->sql_query);
		if(!$subscription_results)
			return false;
		if ($subscription_results->RecordCount()==1)
		{
			$subscription = $subscription_results->FetchRow();
			$this->subscription_info .= "<tr>\n\t<td  class=field_labels>".urldecode($this->messages[743]).": </font>\n\t</td>\n\t";
			$this->subscription_info .= "<td class=data_values>\n\t".date("M d, Y H:i:s", $subscription["expiration"])."\n\t</td>\n</tr>\n";
		}
		else
		{
			$this->subscription_info .= "";
			//$this->body .= "<td class=data_values>\n\t".urldecode($this->messages[743])." ".date("M d, Y H:i:s", $subscription->SUBSCRIPTION_EXPIRE)."\n\t</td>\n</tr>\n";
		}
		//}
		$this->subscription_info .= "<tr>\n\t<td  class=field_labels width=50%>".urldecode($this->messages[1649]).":</font>\n\t</td>\n\t";
		$this->subscription_info .= "<td width=50%  class=data_values>\n\t<a href=".$this->configuration_data['classifieds_file_name']."?a=200 class=data_values>".urldecode($this->messages[1650])."</td>\n</tr>\n";
		
		$this->subscription_info .= "\n\t</table></td>\n\t</tr>";
		
		return $this->subscription_info;
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function editHomeLink($db, $homeLink)
	{
		$homeLink = strip_tags($homeLink);
		$this->sql_query="update ".$this->userdata_table." set 
		storefront_home_link =
		'".addslashes($homeLink)."' 
		where id = '".$this->storefront_id."'
		";
		if(!$db->Execute($this->sql_query))
			return false;
				
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}
	
?>