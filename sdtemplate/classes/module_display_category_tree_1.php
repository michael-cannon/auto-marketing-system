<? //module_display_category_tree_1.php	
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_css($db,$show_module['page_id']);
$this->get_text($db,$show_module['page_id']);
$this->body = "";
if ($this->site_category)
{
	$category_tree = $this->get_category_tree($db,$this->site_category);
	reset ($this->category_tree_array);
	if ($category_tree)
	{
		//category tree
		$this->body .="<table width=100% cellpadding=1 class=category_tree_1><tr>\n\t<td>\n\t";
		$this->body .=urldecode($this->messages[1522])." <a href=".$this->configuration_data['classifieds_file_name']."?a=5&c=".$browse_type." class=category_tree_main_1>".$this->messages[1521]."</a> > ";
		if (is_array($this->category_tree_array))
		{
			$i = 0;
			//$categories = array_reverse($this->category_tree_array);
			$i = count($this->category_tree_array);
			while ($i > 0 )
			{
				//display all the categories
				$i--;
				if ($i == 0)
					$this->body .=$this->category_tree_array[$i]["category_name"];
				else
					$this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->category_tree_array[$i]["category_id"]."&c=".$browse_type." class=category_tree_1>".$this->category_tree_array[$i]["category_name"]."</a> > ";
			}
		}
		else
		{
			$this->body .=$category_tree;
		}
		$this->body .="\n\t</td>\n</tr></table>\n";
	}
}
?>