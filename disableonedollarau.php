<?php
require_once( 'config.php' );

// account for original 1 inventory
$cpPriceDelta					+= 1;

$db_user = $cpConfig['mi_login'];
$db_pass = $cpConfig['db_password'];
$db_host = "localhost";
$db_name = "";

$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once('classes/db_class.php');

$conn = new DB($db_host, $db_user, $db_pass, $db_name);
$conn->open();
		
$q = $conn->query("SHOW DATABASES");
if($q) {
	$i = 0;
	$conn1 = new DB($db_host, $db_user, $db_pass, $db_name);
	$conn1->open();
	while ($res = $conn->fetchRow()) {	        	      
		if(mysql_select_db($res[0], $conn1->conn)) {	          
		   $q1 = $conn1->query("SELECT COUNT(id) as num, category FROM geodesic_classifieds WHERE (minimum_bid <= $cpPriceDelta OR starting_bid <= $cpPriceDelta OR reserve_price <= $cpPriceDelta) AND item_type = 2 AND live = 1 GROUP BY category"); 
		   if($q1) {	              	              
			   while($r = $conn1->fetchRow()) {	                 
				   $conn2 = new DB($db_host, $db_user, $db_pass, $db_name);
				   $conn2->open();
				   $conn2->query("UPDATE geodesic_categories SET category_count = category_count - '".$r[0]."', auction_category_count = auction_category_count - '".$r[0]."' WHERE category_id='".$r[1]."'");
				   $i += $r[0];	                   
				   $conn2->query("UPDATE geodesic_classifieds SET live = 0 WHERE (minimum_bid <= $cpPriceDelta OR starting_bid <= $cpPriceDelta OR reserve_price <= $cpPriceDelta) AND item_type = 2 AND live = 1");
				   $conn2->close();
			   }
		   }
		}
	}	

	print 'Number of updated records: '.$i;    
}

?>