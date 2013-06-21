<?php
include_once('classes/site_class.php');
class Store extends Site
{
	var $storefront_categories_table = "geodesic_storefront_categories";
	var $storefront_display_table = "geodesic_storefront_display";
	var $storefront_pages_table = "geodesic_storefront_pages";
	var $storefront_subscriptions_table = "geodesic_storefront_subscriptions";
	var $storefront_users_table = "geodesic_storefront_users";
	var $storefront_subscriptions_choices_table = "geodesic_storefront_subscriptions_choices";
	var $storefront_group_subscriptions_choices_table = "geodesic_storefront_group_subscriptions_choices";
	var $storefront_template_modules_table = "geodesic_storefront_template_modules";
	
	var $storefront_id;
	var $javascriptHeadScriptOut = "<script language='Javascript'>";
	var $storefrontManagementError = '';
	
	var $ajax = false;
	var $pageId = 0;
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function Store($db,$message_category=0,$language_id=0,$classified_user_id=0,$product_configuration=0,$storefront_id=0)
	{
		$this->storefront_id = $storefront_id;
		$this->storefrontIsEditable = $this->isStorefrontEditable($classified_user_id);
		$this->getStorefrontUserData($db);
		$this->Site($db,$message_category,$language_id,$classified_user_id,$product_configuration);
	}
	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_storefront_configuration($db)
	{
		$this->sql_query = "select * from ".$this->storefront_display_table;
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<bR>\n";
		if (!$result)
		{
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$this->storefront_configuration = $result->FetchRow();
			return true;
		}
		else
		{
			//just display the user_id
			return false;
		}

	} //end of function get_category_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// isStorefrontEditable
	function isStorefrontEditable($browsingUserId)
	{
		if($this->storefront_id == $browsingUserId)
			return true;
		else
			return false;
	}
// end function
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// getStorefrontCategories
	function getStorefrontCategories($db)
	{
		$this->storefrontCategories = array();
		$this->sql_query = "select * from ".$this->storefront_categories_table." where user_id = '".$this->storefront_id."' order by display_order asc";
		$categoryResults = $db->Execute($this->sql_query);
		while($categoryInfo = $categoryResults->FetchRow())
		{
			array_push($this->storefrontCategories, $categoryInfo);
		}
	}
// end function
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// getStorefrontCategories
	function getStorefrontCategoryName($db, $categoryId)
	{
		$this->sql_query = "select * from ".$this->storefront_categories_table." where user_id = '".$this->storefront_id."' and category_id = $categoryId";
		$categoryResults = $db->Execute($this->sql_query);
		$categoryInfo = $categoryResults->FetchRow();
		return $categoryInfo["category_name"];
	}
// end function
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// getStorefrontUserData
	function getStorefrontUserData($db)
	{
		$this->storefrontUserData = null;
		$this->sql_query = "select 
		username, 
		storefront_header, 
		storefront_template_id, 
		storefront_welcome_message, 
		storefront_home_link,  
		storefront_on_hold from ".$this->userdata_table." where id = '".$this->storefront_id."'";
		$categoryResults = $db->Execute($this->sql_query);
		if($categoryResults)
		{
			$this->storefrontUserData = $categoryResults->FetchRow();
		}
	}
// end function
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// getStorefrontUsers
	function getStorefrontUsers($db)
	{
		$this->storefrontUsers = array();
		$this->sql_query = "select * from ".$this->storefront_users_table." where store_id = '".$this->storefront_id."'";
		$categoryResults = $db->Execute($this->sql_query);
		while($categoryInfo = $categoryResults->FetchRow())
		{
			array_push($this->storefrontUsers, $categoryInfo);
		}
	}
// end function
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// getStorefrontUsers
	function getStorefrontImageDir($db)
	{
		$this->sql_query = "select image_upload_path from ".$this->ad_configuration_table;
		$configurationResults = $db->Execute($this->sql_query);
		if ($configurationInfo = $configurationResults->FetchRow())
		{
			if(!$configurationInfo["image_upload_path"])
			{
				$this->storefrontManagementError .= 'No image path specified.';
				return false;
			}
			if(!is_dir($configurationInfo["image_upload_path"]."storefrontImages/"))
				mkdir($configurationInfo["image_upload_path"]."storefrontImages/");
			$this->storefrontImageDir = $configurationInfo["image_upload_path"]."storefrontImages/";
		} else {			
			return false;
		}
		return true;
	}
// end function
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// getStorefrontCategories
	function getStorefrontPages($db)
	{
		$this->storefrontPage = array();
		$this->sql_query = "select * from ".$this->storefront_pages_table." where user_id = '".$this->storefront_id."'";
		$pageResults = $db->Execute($this->sql_query);
		while($pageInfo = $pageResults->FetchRow())
		{
			array_push($this->storefrontPage, $pageInfo);
		}
	}
// end function
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// isStorefrontCategory
	function isStorefrontCategory($db,$categoryId)
	{
		$this->getStorefrontCategories($db);
		foreach($this->storefrontCategories as $storefrontCategoryIndex => $storefrontCategoryDetails)
		{
			if($storefrontCategoryDetails["category_id"]==$categoryId)
			{
				return true;
			}
		}
		return false;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// getStorefrontSubscription
	function getStorefrontSubscription($db)
	{
		$this->sql_query = "select * from ".$this->storefront_subscriptions_table." where user_id = ".$this->storefront_id;
		$subscriptionResult = $db->Execute($this->sql_query);
		if(!$subscriptionResult)
			return false;
		if($subscriptionResult->RecordCount()==1)
		{
			$subscriptionInfo = $subscriptionResult->FetchRow();
			$expiresAt = $this->shifted_time($db) + $subscriptionInfo["expiration"];
			if(time()>=$expiresAt)
				return false;
		}
		else
			return false;
		return true;
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get($variableName)
	{
		$storefront_vars = get_class_vars("Store");
		return $storefront_vars[$variableName];
	} //end of function get

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function get_template_modules($db)
	{
		// Flag if any HTML modules are used
		$html = false;

		$this->sql_query = "select * from ".$this->pages_modules_table." where page_id = ".$this->page_id." order by time asc";
		$result = $db->Execute($this->sql_query);
		if ($this->debug) echo $this->sql_query."<br>\n";
		if (!$result)
		{
			if ($this->debug) echo $this->sql_query."<br>\n";
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			while ($show = $result->FetchRow())
			{
				$this->sql_query = "select * from ".$this->pages_table." where page_id = ".$show['module_id'];
				$module_result = $db->Execute($this->sql_query);
				if ($this->debug) echo $this->sql_query."<br>\n";
				if (!$module_result)
				{
					if ($this->debug) echo $this->sql_query."<br>\n";
					//continue with next module...instead of erroring out.
					continue;
				}
				elseif ($module_result->RecordCount() == 1)
				{
					$show_module = $module_result->FetchRow();
					if (strlen($show_module['module_file_name']) > 0)
					{
						include("classes/".$show_module['module_file_name']);
						$this->template = str_replace($show_module['module_replace_tag'],$this->body,$this->template);
					}

					// Check if HTML is in the module name
					if(strpos($show_module['name'], "HTML") != false)
					{
						$html = true;
					}
				}
			}
		}

		// If we there were any HTML modules lets go over the modules again so that there are no embedded ones
		/*if($html)
		{
			$this->sql_query = "select * from ".$this->pages_table." where module = 1";
			$module_result = $db->Execute($this->sql_query);

			$modules = $module_result->GetArray();
			foreach($modules as $array => $key)
			{
				//include("classes/".$key['module_file_name']);
				$this->template = str_replace($key['module_replace_tag'], $this->body, $this->template);
			}
		}
		*/

		return true;
	} // end of function get_page_modules

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}
?>