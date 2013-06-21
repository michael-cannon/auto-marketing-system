<?php
class Admin_store extends Admin_site
{
	var $storefront_categories_table = "geodesic_storefront_categories";
	var $storefront_display_table = "geodesic_storefront_display";
	var $storefront_pages_table = "geodesic_storefront_pages";
	var $storefront_subscriptions_table = "geodesic_storefront_subscriptions";
	var $storefront_users_table = "geodesic_storefront_users";
	var $storefront_subscriptions_choices_table = "geodesic_storefront_subscriptions_choices";
	var $storefront_group_subscriptions_choices_table = "geodesic_storefront_group_subscriptions_choices";
	var $storefront_template_modules_table = "geodesic_storefront_template_modules";
	
//########################################################################
	
	function Admin_store($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);

		// Set the icon in the admin
		$this->admin_icon = "admin_images/menu_storefront.gif";
	}

//########################################################################

	function get_row_color($row_count=0)
	{
		if(!$row_count)
			$row_count=$this->row_count;
		if (($row_count % 2) == 0)
			$row_color = "row_color1";
		else
			$row_color = "row_color2";
		return $row_color;
	} //end of function get_row_color

//########################################################################

	function get($variableName)
	{
		$storefront_vars = get_class_vars("Admin_store");
		return $storefront_vars[$variableName];
	} //end of function get

//########################################################################
}
?>