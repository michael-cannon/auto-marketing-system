<?

$file = file_get_contents("../index.php");
$file .= file_get_contents("bulk_uploader_class.php");
$file .= file_get_contents("preview_window.php");

$css = ".profile_table
{
	background: #FFFFFF;
	font-family: Arial,Helvetica, sans-serif;
	font-size: 12pt;
	font-style: normal;
	line-height: normal;
	color: #000000;
	text-decoration: none;
}\n";
$big_string = "";

preg_match_all('/class=\\\"[a-zA-Z0-9_]*\\\"/', $file, $matches);

sort($matches[0]);

foreach($matches[0] as $key => $value)
{
	//echo $value.'<Br>';
	$value = str_replace("class=\\\"", "", $value);
	$value = str_replace("\\\"", "", $value);
	//echo $value.'<Br>';

	$value = str_replace("profile_table", $value, $css);

	$big_string .= $value;

	file_put_contents("bulk.css", $big_string);
}
echo 'Done<Br>';
?>