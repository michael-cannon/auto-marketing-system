<?
/*	NOTE The following variables are needed to be passed into this:
 *	category_id
 *	delimeter
 *	encapsulation
 *	listing_type
 *	file_name
 */

//highlight_string(print_r($_REQUEST,1));

include("../../config.php");
include("../../classes/adodb.inc.php");

$db = &ADONewConnection('mysql');

if (!$db->Connect($db_host, $db_username, $db_password, $database))
{
	echo "could not connect to database";
	exit;
}

include_once('../../products.php');
$product_configuration = new product_configuration($db, $product_type, 1);

include_once('bulk_uploader_class.php');
$bulk = new bulk_uploader($db, $_REQUEST["category_id"], $product_configuration, $_REQUEST['listing_type']);

@$handle = fopen($_SERVER['DOCUMENT_ROOT'].str_replace("preview_window.php", "", $_SERVER['PHP_SELF'])."/uploads/bulk_upload_file.txt", 'r');
if(!$handle)
{
	echo $_REQUEST['file_name'].'<Br>';
	echo 'Error opening temporary file<Br>';
	exit(1);
}

// Put the data from the file in a big 2d array
$data_array = array();
$delimeter = urldecode($_REQUEST['delimeter']);
$encapsulation = urldecode($_REQUEST['encapsulation']);
if($encapsulation)
	while($data_array[] = fgetcsv($handle, 100000, $delimeter, $encapsulation)) {	/*	Do Nothing	*/}
else
{
	// User did not enter an encapsulation so assume there is not one
	while($data_array[] = fgetcsv($handle, 100000, $delimeter)) {	/*	Do Nothing	*/}
}
//highlight_string(print_r($data_array, 1));

// Create length of Array
$size = sizeof($data_array[0]);

// Below this line is the displaying of the page
echo '<html>';
echo '<head>';
echo "<script language=\"javascript\" src=\"parse_form.js\"></script>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"bulk.css\">\n";
echo '</head>';

echo "<body>";

// Show the loading div
// TODO later
//echo "<div id=\"loading_caption\" class=\"loading_caption\">Loading the preview window...</div>";
//echo "<script language=\"javascript\" src=\"show_caption.js\"></script>\n";

// Display the choose display number results block
echo choose_preview_size_block();

echo '<form id="iframe_form">';

// Need to have the type in the file somewhere so it is hidden
// The id must be c[listing_type] the c array is used for the generic function outside the iframe
echo '<input type="hidden" value="'.$_REQUEST["listing_type"].'" id="listing_type">';

echo '<table id="results_table">';
echo "<tr>";
echo '<td></td>';
	
if($_REQUEST['profile_text'])
	$profile_array = explode("|", $_REQUEST['profile_text']);
for($i=0; $i <count($data_array[0]); $i++)
{
		$names[0] = "top".$i;
		$names[1] = "bottom".$i;
		$names[2] = "div".$i;
		$names[3] = "header_row";
	
		// Selected value for display_dual_dropdowns_preview_frame
		// TODO we need to build these
		$selected[0] = isset($profile_array) ? $profile_array[($i*2)] : "-1";
		$selected[1] = isset($profile_array) ? $profile_array[($i*2)+1] : "-1";
		//highlight_string(print_r($profile_array, 1));
		//highlight_string(print_r($selected, 1));
	
		// TODO uncomment when the $selected array is built
		echo '<td>'.display_dual_dropdowns_preview_frame($bulk, $names, $selected).'</td>';
}
echo '</tr>';

$outside_counter = 1;
foreach($data_array as $value)
{
	if($outside_counter % 2)
		$css = "class=\"odd_result_row\"";
	else
		$css = "class=\"even_result_row\"";

	// Check for null values coming from the fgetcsv
	if(!$value)
		continue;

	// Stop at initial value of 50
	// Goes to 51 so that it will display the 50th one
	// Javascript will change this dropdown if more is selected
	if($outside_counter == 51)
		break;

	echo '<tr '.$css.'>';

	// Put first numerical column
	echo "<td class=\"listing_counter\">".$outside_counter.'</td>';
	for($i = 0; $i < $size; $i++)
		echo '<td>'.$value[$i].'</td>';
	echo "</tr>\n";

	$outside_counter++;
}
echo '</table>';
echo '</form>';
echo '</body>';
echo '</html>';

$db->Close();

function choose_preview_size_block()
{
	$entry_label = "Choose how many results you wish to display:";

	$body .= "<table class=\"choose_preview_table\">";
	$body .= "<tr class=\"choose_preview_row\">\n\t";
	$body .= "<td class=\"choose_preview_label\" colspan=2>\n\t".$entry_label."</td>";
	$body .= "<td class=\"choose_preview_list\" colspan=2>\n\t";
	$body .= "<select class=\"choose_preview_select\" id=\"choose_preview_select\" onClick=\"SendResultSize()\">\n\t";
	$body .= "<option value=\"50\">50</option>\n";
	$body .= "<option value=\"100\">100</option>\n";
	$body .= "<option value=\"200\">200</option>\n";
	$body .= "<option value=\"300\">300</option>\n";
	$body .= "</select>";
	$body .= "</td>";
	$body .= "</tr>";
	$body .= "</table>";

	return $body;
}

function display_dual_dropdowns_preview_frame($bulk, $names, $selected)
{
	// $names is the names of the top, bottom, div, and class
	// $names[0] = $top_name
	// $names[1] = $bottom_name
	// $names[2] = $div_name
	// $names[3] = $class_name

	// selected controls what is selected in the top and bottom dropdowns
	// $selected[0] is the top select
	// $selected[1] is the bottom select
	$top_dropdown .= "<select id=\"".$names[0]."\" name=\"".$names[0]."\" onClick=\"SendDropDownChange('".$names[0]."', '".$names[1]."', '".$names[2]."')\"";
	$top_dropdown .= " class=\"".$names[3]."\"";
	$top_dropdown .= ">";
	$top_dropdown .= "<option value=\"-1\"";
	if($selected[0] == -1)
		$top_dropdown .= " selected";
	$top_dropdown .= ">Field Not Used</option>";
	foreach ($bulk->top_dropdown as $key => $value)
	{
		$top_dropdown .= "<option value=\"".$key."\"";
		if($selected[0] == $key)
			$top_dropdown .= " selected";
		$top_dropdown .= ">".$value."</option>\n";
	}
	$top_dropdown .= "</select>";
	$body = $top_dropdown;

	$bottom_dropdown = "<div id=\"".$names[2]."\">";
	$bottom_dropdown .= "<select id=\"".$names[1]."\" name=\"".$names[1]."\" class=\"".$names[3]."\" onChange='javascript: checkOtherColumns(this.id, this.options[this.selectedIndex].innerHTML);'>";
	$bottom_dropdown .= $bulk->get_bottom_dropdown($selected[0], $selected[1]);
	$bottom_dropdown .= "</select>";
	$bottom_dropdown .= "</div>";
	$body .= $bottom_dropdown;

	return $body;
}
?>