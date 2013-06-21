<?

class product
{
	// is a class auction;
	var $type = 3;
	
	function product()
	{
	}
	
	function select_correct_items($field_name="applies_to")
	{
		if($this->type == 3)
			return "($field_name = 1 or $field_name = 2)";
		else 
			return "$field_name = {$this->type}";
	}
}

?>