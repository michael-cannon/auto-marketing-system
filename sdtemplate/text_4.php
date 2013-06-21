<?php
/**
 * Inventory file reader and importer
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: text_4.php,v 1.1.1.1 2010/04/15 09:42:45 peimic.comprock Exp $
 */
require_once( 'config.php' );
set_time_limit(60 * 60 * 60 * 4);

$delta_minus = 1495;
$delta_plus = $delta_minus;
$bid = 0;

$subdomain = basename( dirname( __FILE__ ) );

$monthes = Array('January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12);

//Set start date and time
$mstart = $_REQUEST['mstart'];
$hstart = $_REQUEST['hstart'];
$dstart = trim($_REQUEST['datestart']);

$datestart = explode(" ", $dstart);

$datestart[0] = $monthes[$datestart[0]];

$start_time = mktime($hstart, $mstart, 0, $datestart[0], $datestart[1], $datestart[2]);
//Set start date and time

//Set convert classifieds to auctions date and time
$mconvert = $_REQUEST['mconvert'];
$hconvert = $_REQUEST['hconvert'];
$dconvert = trim($_REQUEST['dateconvert']);

$dateconvert = explode(" ", $dconvert);

$dateconvert[0] = $monthes[$dateconvert[0]];

$convert_time = mktime($hconvert, $mconvert, 0, $dateconvert[0], $dateconvert[1], $dateconvert[2]);
//Set convert classifieds to auctions date and time

//Set end date and time
$mend = $_REQUEST['mend'];
$hend = $_REQUEST['hend'];
$dend = trim($_REQUEST['dateend']);

$dateend = explode(" ", $dend);

$dateend[0] = $monthes[$dateend[0]];

$end_time = mktime($hend, $mend, 0, $dateend[0], $dateend[1], $dateend[2]);
$ends = $end_time;
//Set end date and time

if(!empty($_REQUEST['classified_length'])) $classified_length = $_REQUEST['classified_length'];
if(!empty($_REQUEST['auction_type'])) $auction_type = $_REQUEST['auction_type'];
if(!empty($_REQUEST['auction_quantity'])) $auction_quantity = $_REQUEST['auction_quantity'];
if(!empty($_REQUEST['auction_buy_now'])) $auction_buy_now = $_REQUEST['auction_buy_now'];
if(!empty($_REQUEST['payment_options'])) $payment_options = $_REQUEST['payment_options'];
if(!empty($_REQUEST['price_plan_id'])) $price_plan_id = $_REQUEST['price_plan_id'];
if(!empty($_REQUEST['payment_type'])) $payment_type = $_REQUEST['payment_type'];
if(!empty($_REQUEST['featured_ad'])) $featured_ad = $_REQUEST['featured_ad'];
if(!empty($_REQUEST['item_type'])) $item_type = $_REQUEST['item_type'];

if(!empty($_REQUEST['delta_minus'])) $delta_minus = $_REQUEST['delta_minus'];
if(!empty($_REQUEST['delta_plus'])) $delta_plus = $_REQUEST['delta_plus'];

if($geobu_debug >= 1) echo "\$_FILES <pre>".print_r($_FILES,true)."</pre><br/>";

// user's images reside in folder named by user_id
$geobu_uploaddir = 'user_images/';  // in case $sql query has problems, revert to default
$sql = "SELECT url_image_directory FROM geodesic_classifieds_ad_configuration";
$results = $db->Execute($sql);
if($results)
{
	if(1 == $results->RecordCount())
	{
		$show = $results->FetchNextObject();
		$geobu_uploaddir = $show->URL_IMAGE_DIRECTORY;
	}
}

// Strip any trailing slashes for consistency
if('/' == substr($geobu_uploaddir, -1)) $geobu_uploaddir = substr($geobu_uploaddir, 0, -1);
//$geobu_uploaddir = basename($geobu_uploaddir);
//Strip off unnecessary leading pathname - gives nothing if ending in /
$geobu_uploaddir .= "/".$user_id."/";

if(!file_exists($geobu_uploaddir))
{
	$result = mkdir($geobu_uploaddir);
}
// store images in folder corresponding to userID - username was an option, but requires more code
$uploadfile = $geobu_uploaddir . basename($_FILES['datafile']['name']);

if($geobu_debug >= 1) echo "\$uploadfile: ".$uploadfile."<br/>";

if(isset($_FILES['datafile']['name']))
{
	if (move_uploaded_file($_FILES['datafile']['tmp_name'], $uploadfile))
	{
		echo "<p><p><h3><center>The data file appears to be valid, and was successfully uploaded.  Now processing...</center></h3><br/>\n";
		echo "<table align=center border='0' cellpadding=2 cellspacing=1 bgcolor=1 class=blue>\n";
		{
			// prepare for loop through data
			$total=0;    // keep track of total cost of ads
			$first_row = true;
			$nrow = 0;
			$nsuccess = 0;    // track number of successful uploads
			$nworksheet = 0;
			// ********************************  tested with php5, but most users using php4 *************

			if(true || $php_version == 'php4')
			{
				//New BU code
				$user_data = $auth->get_user_data($db, $user_id);
				$csvfile = dirname(__FILE__) . '/' . $uploadfile;

				$file = fopen($csvfile, "r");

				$first_line = 1;
				$first_row = 1;
				$row = 0;

				$replacement = Array(
				'status' => 'optional_field_1',
				'type(new/used)' => 'optional_field_1',
				'new/used' => 'optional_field_1',
				'usednew' => 'optional_field_1',
				'newused' => 'optional_field_1',
				'isused' => 'optional_field_1',
				'inventorytype' => 'optional_field_1',
				'type' => 'optional_field_1',
				'year' => 'optional_field_2',
				'series' => 'optional_field_3',
				'trim' => 'optional_field_3',
				'body style' => 'optional_field_4',
				'bodystyle' => 'optional_field_4',
				'body_style' => 'optional_field_4',
				'bodytype' => 'optional_field_4',
				'body' => 'optional_field_4',
				'style' => 'optional_field_4',
				'mileage' => 'optional_field_5',
				'miles' => 'optional_field_5',
				'odom' => 'optional_field_5',
				'transmission type' => 'optional_field_6',
				'transmission' => 'optional_field_6',
				'tramsission' => 'optional_field_6',
				'trans_text' => 'optional_field_6',
				'engine type' => 'optional_field_7',
				'engine' => 'optional_field_7',
				'enginedisplacement' => 'optional_field_7',
				'displacement' => 'optional_field_7',
				'fuel type' => 'optional_field_8',
				'fuel_type' => 'optional_field_8',
				'fuel' => 'optional_field_8',
				'engine_type' => 'optional_field_8',
				'drive train' => 'optional_field_9',
				'drive train type' => 'optional_field_9',
				'drivetrain' => 'optional_field_9',
				'drivenwheels' => 'optional_field_9',
				'number of doors' => 'optional_field_10',
				'doors' => 'optional_field_10',
				'door' => 'optional_field_10',                    
				'interior color' => 'optional_field_11',
				'interiorcolor' => 'optional_field_11',
				'intcolor' => 'optional_field_11',
				'interior' => 'optional_field_11',
				'interior material' => 'optional_field_12',
				'exterior' => 'optional_field_13',
				'exterior color' => 'optional_field_13',
				'exteriorcolor' => 'optional_field_13',
				'color' => 'optional_field_13',
				'extcolor' => 'optional_field_13',
				'title status' => 'optional_field_14',
				'warranty status' => 'optional_field_15',
				'warranty' => 'optional_field_15',
				'vin' => 'optional_field_16',
				'inspection status' => 'optional_field_17',
				'stocknumber' => 'optional_field_18',
				'stock #' => 'optional_field_18',
				'stock#' => 'optional_field_18',
				'stockno' => 'optional_field_18',
				'stock number' => 'optional_field_18',
				'stock' => 'optional_field_18',
				'stockid' => 'optional_field_18',
				'stocknumber' => 'optional_field_18',
				'inventory cost' => 'price',
				'cost' => 'price',
				'sellingprice' => 'price',
				'webprice' => 'price',
				'duration' => 'classified_length',
				'sell_type' => 'auction_type',
				'amenities'=>'options',
				'features'=>'options',
				'standard features'=>'options',
				'photourl' => 'image',
				'photo_url' => 'image',
				'imageurls' => 'image',
				'imageURLs' => 'image',
				'photolinks' => 'image',
				'photo 1 url' => 'image',
				'images' => 'image',
				'thumbnailimage' => 'image',
				);

				while(($data = fgetcsv($file, 5000, ",")) !== FALSE)
				{
					if(!$row)
					{
						$bukeys = $data;
						$row ++;

						foreach($bukeys as $key => $value)
						{
							$value = strtolower($value);
							if(!empty($replacement[$value])) $bukeys[$key] = $replacement[$value];
							else $bukeys[$key] = $value;
						}
						$first_line = 0;
					}
					else
					{
						$bu = Array();
						$first_row = 0;
						$countData	= count($data);

						for($i = 0; $i < $countData; $i ++)
						{
							$data[$i] = eregi_replace("&quot;", "\"", $data[$i]);
							$data[$i] = eregi_replace("&gt;", ">", $data[$i]);

							// remove uk/uknown content entries
							if(strtolower($bukeys[$i]) == 'uk' ||
								strtolower($bukeys[$i]) == 'unknown')
							{
								$bukeys[$i] = '';
							}

							if($bukeys[$i] == 'image')
							{
								$data[$i] = preg_replace("#(\n|\s+)#"
									, ','
									, $data[$i]
								);
								$data[$i] = preg_replace("#\?[[:alnum:]]+,?#"
									, ','
									, $data[$i]
								);
								$data[$i] = str_replace('|', ',', $data[$i]);
								$data[$i] = str_replace(',,', ',', $data[$i]);
								$data[$i] = preg_replace("#^,$#"
									, ''
									, $data[$i]
								);
							}

							if($bukeys[$i] == 'optional_field_1')
							{
								$shorthand = Array(
									"/\bused\b/i"
									, "/\bu\b/i"
									, "/\bnew\b/i"
									, "/\bn\b/i"
									, "/\b1\b/i"
								);
								$words = Array("Used", "Used", "New", "New","Used");
								$data[$i] = preg_replace($shorthand, $words, $data[$i]);
							}

							if(false && $bukeys[$i] == 'optional_field_6') {
								$shorthand = Array("a", "m");
								$words = Array("Automatic", "Manual");
								$data[$i] = str_replace($shorthand, $words, $data[$i]);
							}
							if($bukeys[$i] == 'optional_field_7')
							{
								$shorthand = Array("Overd");
								$words = Array(" overhead");
								$data[$i] = str_replace($shorthand, $words, $data[$i]);
							}

							if($bukeys[$i] == 'optional_field_13') {
								$shorthand = Array("Metall", "meet", "metal", "Metall", "Metalliclicic", "metalliclicic");
								$words = Array(" metallic", " metallic", " metallic", " metallic", " metallic", " metallic");
								$data[$i] = str_replace($shorthand, $words, $data[$i]);
							}

							if($bukeys[$i] != 'optional_field_18'
								&& $bukeys[$i] != 'optional_field_16'
								&& $bukeys[$i] != 'image'
							)
							{
								$bu[$bukeys[$i]] = (is_numeric($data[$i])) ? preg_replace("#\.0$#", '', ucwords(strtolower(addslashes(trim($data[$i]))))) : ucwords(strtolower(addslashes(trim($data[$i]))));
							}
							else $bu[$bukeys[$i]] = addslashes(trim($data[$i]));
						}
					
						if(!$bu['optional_field_1']) {
							$bu['optional_field_1'] = 'Used';
						}

						// convert 0 miles and current year vehicles to New
						if( (date('Y') == $bu['optional_field_2']
							|| (date('Y') - 1) == $bu['optional_field_2'] )
							&& ! $bu['optional_field_5']) {
							$bu['optional_field_1'] = 'New';
						}
						
						// $bu['options'] = strtolower( $bu['options'] );
						$bu['options'] = join(", ", explode("|", $bu['options']));
						$bu['options'] = preg_replace("#,\s*#", ", ", $bu['options']);
						$bu['options'] = preg_replace("#\bpwr\b#i", "power", $bu['options']);
						$bu['options'] = preg_replace("#\bcd\b#i", "CD", $bu['options']);
						$bu['options'] = preg_replace("#\babs\b#i", "ABS", $bu['options']);
						$bu['options'] = preg_replace("#\ba/c\b#i", "A/C", $bu['options']);
						$bu['options'] = ( $bu['comments'] )
							? $bu['comments'] . '<br /><br />' . $bu['options']
							: $bu['options'];
						//$bu['optional_field_16'] = strtoupper($bu['optional_field_16']);

						$bu['make'] = eregi_replace("^MERC$", "Mercury", $bu['make']);

						$bu['optional_field_9'] = str_replace("-", "", $bu['optional_field_9']);
						$bu['optional_field_3'] = ($bu['optional_field_3'])
							? $bu['optional_field_3']
							: $bu['optional_field_9'];

						$modelp = explode(" ", $bu['model']);
						$model_new = '';
						$countModelp = count($modelp);
						for($m = 0; $m < $countModelp; $m ++)
						{
							if(strlen($modelp[$m]) < 4 && $modelp[$m] != 'Am' && $modelp[$m] != 'Ram') $modelp[$m] = strtoupper($modelp[$m]);
							$model_new .= ($model_new) ? " ".$modelp[$m] : $modelp[$m];
						}
						$bu['model'] = $model_new;
						$bu['model'] = str_replace("-", "", $bu['model']);

						if(strlen($bu['make']) < 4) $bu['make'] = strtoupper($bu['make']);

						if(!$bu['classified_title']) $bu['classified_title'] = $bu['optional_field_2']." ".$bu['make']." ".$bu['model']." ".$bu['optional_field_3'];

						$titlep = explode(" ", $bu['classified_title']);
						$title_new = '';
						$countTitlep = count($titlep);
						for($m = 0; $m < $countTitlep; $m ++)
						{
							if(strlen($titlep[$m]) < 4
								&& $titlep[$m] != 'Am'
								&& $titlep[$m] != 'Ram'
							)
							{
								$titlep[$m] = strtoupper($titlep[$m]);
							}
							$title_new .= ($title_new) ? " ".$titlep[$m] : $titlep[$m] ;
						}
						$bu['classified_title'] = $title_new;

						if(!$bu['category']) $bu['category'] = $bu['make']." > ".$bu['model'];

						//BCS-IT 02.05.2006
						//Default fields
						if(!$bu['mapping_address']) $bu['mapping_address'] = addslashes($user_data->ADDRESS);
						if(!$bu['mapping_city']) $bu['mapping_city'] = addslashes($user_data->CITY);
						if(!$bu['mapping_state']) $bu['mapping_state'] = addslashes($user_data->STATE);
						if(!$bu['mapping_country']) $bu['mapping_country'] = addslashes($user_data->COUNTRY);
						if(!$bu['mapping_zip']) $bu['mapping_zip'] = addslashes($user_data->ZIP);
						if(!$bu['city']) $bu['city'] = addslashes($user_data->CITY);
						if(!$bu['state']) $bu['state'] = addslashes($user_data->STATE);
						if(!$bu['country']) $bu['country'] = addslashes($user_data->COUNTRY);
						if(!$bu['zip_code']) $bu['zip_code'] = addslashes($user_data->ZIP);
						if(!$bu['email_option']) $bu['email_option'] = addslashes($user_data->EMAIL);
						if(!$bu['expose_email']) $bu['expose_email'] = addslashes($user_data->EXPOSE_EMAIL);
						//if(!$bu['phone_1_option']) $bu['phone_1_option'] = '1-'.$user_data->PHONE;
						if(!$bu['phone_1_option']) $bu['phone_1_option'] = $user_data->PHONE;

						if(!$bu['classified_length'])
						{
							if($classified_length) $bu['classified_length'] = $classified_length;
							else $bu['classified_length'] = 9;
						}

						if(!$bu['auction_type'])
						{
							if($auction_type) $bu['auction_type'] = $auction_type;
							else $bu['auction_type'] = 1;
						}

						if(!$bu['auction_quantity'])
						{
							if($auction_quantity) $bu['auction_quantity'] = $auction_quantity;
							else $bu['auction_quantity'] = 1;
						}

						if(!$bu['auction_buy_now'])
						{
							if($auction_buy_now) $bu['auction_buy_now'] = $auction_buy_now;
							else $bu['auction_buy_now'] = 0;
						}

						if(!$bu['payment_options'])
						{
							if($payment_options) $bu['payment_options'] = $payment_options;
							else $bu['payment_options'] = "Visa||Mastercard||Discover||American Express||Check||Money Order||PayPal||Bank Transfer";
						}

						if(!$bu['price_plan_id'])
						{
							if($price_plan_id) $bu['price_plan_id'] = $price_plan_id;
							else $bu['price_plan_id'] = 9;
						}

						if(!$bu['payment_type'])
						{
							if($payment_type) $bu['payment_type'] = $payment_type;
							else $bu['payment_type'] = 7;
						}

						if(!$bu['featured_ad'])
						{
							if($featured_ad) $bu['featured_ad'] = $featured_ad;
							else $bu['featured_ad'] = 1;
						}

						if(!$bu['item_type'])
						{
							if($item_type) $bu['item_type'] = $item_type;
							else $bu['item_type'] = 1;
						}

						$bu['sell_type'] = $bu['item_type'];
						
						$img_arr = explode(",",  $bu['image']);
						$countImg_arr = count($img_arr);

						if(is_array($img_arr)) {
						  $bu['image'] = $img_arr[0];
						  for($i = 1; $i<$countImg_arr; $i++) {
							$bu['image'.($i+1)] = $img_arr[$i];
						  }
						}

						//Image titles
						for($i = 1; $i <= $countImg_arr; $i ++)
						{
							if($i == 1) $bu['image_text'] = $bu['classified_title'];
							else
							{
								$img = 'image'.$i;
								if(!empty($bu[$img]))
								{
									$str = 'image'.$i.'_text';
									$bu[$str] = $bu['classified_title'];
								}
							}
						}
													
						$bu['price'] = str_replace("$", "", $bu['price']);
						$bu['price'] = str_replace(",", "", $bu['price']);
						
						$bu['price'] = intval($bu['price']);
						if($bu['price'] == 0) {
						   $bu['invoice/cost'] = str_replace("$", "", $bu['invoice/cost']);
						   $bu['invoice/cost'] = str_replace(",", "", $bu['invoice/cost']); 
						   $bu['price'] = intval($bu['invoice/cost']) + $delta_plus + $delta_minus;
						}
						if($bu['price'] > 0)
						{
							if($bu['price'] > $delta_minus)
							{
								$bu['auction_minimum'] = $bu['price'] - $delta_minus;
								$bu['auction_reserve'] = $bu['price'] + $delta_plus;
								$bu['price'] = 0;
							}
							else
							{
								$bu['auction_minimum'] = 1;
								$bu['auction_reserve'] = $bu['price'] + $delta_plus;
								$bu['price'] = 0;
							}
						}
					}

					$bu['optional_field_4'] = preg_replace("/([0-9]+) ?dr?/i", "\\1 door", $bu['optional_field_4']);
					$bu['optional_field_4'] = preg_replace("/ car/i", "", $bu['optional_field_4']);
					$bu['optional_field_10'] = preg_replace("/([0-9]+) ?dr?/i", "\\1 door", $bu['optional_field_10']);
					$bu['optional_field_7'] = str_replace(" L ", " liter ", $bu['optional_field_7']);
					$bu['optional_field_7'] = preg_replace("#(cyl[\.\s]+)#si", "Cylinder ", $bu['optional_field_7']);
					$bu['optional_field_7'] = preg_replace("/([0-9]{1,2})([\s]*)(cylinder)/i", "\\1-\\3", $bu['optional_field_7']);
// $data[$i] = preg_replace("#(cyl[\.\s]+)#si", " cylinder", $data[$i]);
					$bu['optional_field_5'] = eregi_replace("([1-9]{2})([1-9]{3})", "\\1,\\2", $bu['optional_field_5']);
					$bu['optional_field_11'] = preg_replace("#/(.)#", "/".strtoupper('\\1'), $bu['optional_field_11']);

					if(strlen($$bu['optional_field_3']) < 4) $bu['optional_field_3'] = strtoupper($bu['optional_field_3']);
					if(strlen($$bu['optional_field_9']) < 4) $bu['optional_field_9'] = strtoupper($bu['optional_field_9']);

					$intc = explode("/", $bu['optional_field_11']);
					if(count($intc) > 1) $bu['optional_field_11'] = $intc[0].'/'.ucwords(strtolower(trim($intc[1])));

					if(!preg_match("/,/", $bu['optional_field_5']) && strlen($bu['optional_field_5']) > 3)
					{
						$prlen = strlen($bu['optional_field_5']);
						$pr1 = substr($bu['optional_field_5'], 0, $prlen - 3);
						$pr2 = substr($bu['optional_field_5'], $prlen - 3, 3);
						$bu['optional_field_5'] = $pr1.','.$pr2;
					}

					if(!$bu['description'])
					{
						makeDescription($bu);
					}
					$bu['description'] = eregi_replace("(, ){2,20}.", "", $bu['description']);
					$bu['description'] = str_replace(" .", ".", $bu['description']);
					$bu['description'] .= " Ask for stock number ".$bu['optional_field_18'].".";
					$bu['description'] = eregi_replace("cd$", "CD", $bu['description']);
					$bu['description'] = eregi_replace("am/fm", "AM/FM", $bu['description']);

					$debug = 0;
					if($debug)
					{
						if(count($bukeys) > 0)
						{
							foreach($bukeys as $key => $value)
							{
								echo "$key => $value<br>";
							}
							echo "<hr size=1 color=#000099>";
						}

						if(count($bu) > 0)
						{
							foreach($bu as $key => $value)
							{
								echo "$key => $value<br>";
							}
							echo "<hr size=1 color=#ff9900>";
						}

						foreach($bu as $bkey => $bvalue)
						{
							if($key != 'description')
							{
								$shorthand = Array("Metalliclicic", " .", " Dr ", " Iter", "Tan/neutral");
								$words = Array(" Metallic", ".", " door ", "", "Tan/Neutral");
								$bu[$bkey] = str_replace($shorthand, $words, $bvalue);
							}
						}
					}
					// New BU code


					if(1 <= $geobu_debug)
					{
						if($first_row) echo __LINE__."Line<br/>\$bukeys: <pre>".print_r($bukeys,true)."</pre><br/>\n";
						else echo __LINE__."Line<br/>\$bu: <pre>".print_r($bu,true)."</pre><br/>\n";
					}

					// bulk_sell this item if not first row

					if (!$first_row)
					{
							//$wsname = "worksheet"; //$worksheet->getAttribute('Name');
							//if(null != $wsname) echo "<tr><th colspan='4' bgcolor='#fffaf5' align='left'>".$wsname."</th></tr>\n";

						$bulk_id = bulk_sell( $site, $db, $user_id, $bu );
						$bulks[$bid] = $bulk_id;
						$bid ++;

						if(0 != $bulk_id)  // error condition
							if( 1 <= $geobu_debug) echo "</tr><tr><td colspan=100 align='left' bgcolor='#fffaf5'>Success - ID=$bulk_id</td>";

						if(0 != $bulk_id)
						{
								$query = "UPDATE geodesic_classifieds
										 SET
										 end_time = '".$end_time."',
										 ends = '".$end_time."',
										 start_time = '".$start_time."'
									 WHERE id = ".$bulk_id;

									 echo $query.'<hr>';

							$dateupdate_result = $db->Execute($query);

							// display success information
							echo "<tr><td bgcolor='#fffaf5'>".$bu['classified_title']
							."</td><td bgcolor='#fffaf5'>Uploaded</td><td bgcolor='#fffaf5'>$bulk_id</td><td>".$bu['subtotal']."</td></tr>";
							$total += $bu['subtotal'];
							$nsuccess += 1;
						}
						else
						{
							echo "<tr><td bgcolor='#fffaf5'>".$bu['classified_title']
							."</td><td bgcolor='#fffaf5'><b>Error</b></td><td colspan=2 bgcolor='#fffaf5'>$geobu_error</td></tr>";
						}
						unset($bu);
					}
					else
					{
						echo "<tr><th bgcolor='#fffaf5'>Title</th><th bgcolor='#fffaf5'>Status</th><th bgcolor='#fffaf5'>ID</th><th bgcolor='#fffaf5'>Subtotal</th></tr>";
					}

					if (1 <= $geobu_debug )  echo "</tr>\n";
				}

				if(1 <= $geobu_debug) echo "</table><br/><br/>\n";
			}
			// *********************************** end of php4 version ***********************************
		}

		$total = sprintf(" $ %01.2f", $total);
		echo "<tr><th align=right colspan=3>TOTAL CHARGES:</th><th align=left>$total</th></tr>";

		$skipCrontab		= cbRequest( 'skipCrontab' );
		// MLC 20070912 skip crontab if told to
		if(count($bulks) > 0 && ! $skipCrontab )
		{
						$session_id = session_id();

						if(!$db_cron = mysql_connect($api_db_host, $api_db_username, $api_db_password))
						{
				echo "Can't connect to database.<br> MySql Errno: ".mysql_errno()." Mysql error: ".mysql_error();
				exit();
						}
						if(!mysql_select_db($api_database, $db_cron))
						{
				echo "Can't select database.<br> MySql Errno: ".mysql_errno()." Mysql error: ".mysql_error();
				exit();
						}

			$endtime = $convert_time + 1000;
			$username = explode( '_', $api_db_username );
			$crontab = $mconvert
				." ".$hconvert
				." ".$dateconvert[1]
				." ".$dateconvert[0]
				." * /usr/local/bin/php "
				."/home/"
				. $username[0]
				. "/public_html/cp/convertcstoau.php "
				.$subdomain
				. ' ' . $user_id;
			$query = "INSERT INTO crontab VALUES('', '".$crontab."', ".$endtime.", 1)
			ON DUPLICATE KEY UPDATE crontab = '".$crontab."'
				, endtime = ".$endtime."
				, live = 1
			";

			if (1 <= $geobu_debug)
			{
					echo '<br><br>Sql query: '.$query;
			}

			if(!$res = mysql_query($query))
			{
				echo "Can't execute query.<br> MySql Errno: ".mysql_errno()." Mysql error: ".mysql_error();
	exit();
			}
		}
	}
	// end of if move uploaded file

			{
					// bulk sell using information in $bu
					if (1 <= $geobu_debug)
					{
							//list in table format for excel
						echo "<br/>\n<table border='2'><tr>";
						foreach($bu as $key => $val) echo "<td>".$key."</td>";
						echo "</tr><tr>";
						foreach($bu as $key => $val) echo "<td>".$val."</td>";
						echo "</tr></table><br/>\n";
					}

				echo "<tr><td align='center' colspan='10'><b>$nsuccess Ads successfully uploaded!</b><br/>
				  <a href='index.php?a=4&b=1'>View my ads</a>
					  <p><a href='/admin/index.php?a=70'>Reset Category Count</a></p>
				  </tr></td>";

					   echo "</table>";
			}


		unlink($uploadfile);
		// remove the data file so the customer doesn't accidentally re-process this file by refreshing the page
	}
?>
