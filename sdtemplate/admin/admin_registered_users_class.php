<?php
class Admin_show_registered_users extends Admin_site
{
    function Admin_listings_admin($db, $product_configuration=0)
    {
        //constructor
        $this->Admin_site($db, $product_configuration);

        // Set the admin_icon variable for the admin icon
        $this->admin_icon = "admin_images/menu_storefront.gif";
    }

    function show_user_list($db)
    {
    	$salt = date(Ymd);
        $filename = 'ams-registered-users-' . $salt . '.csv';
        $filePath = '../scripts/' . $filename;

        if(!$file = fopen($filePath, "w"))
        {
            echo "<br>Can't open file $filename<br>";
            exit;
        }

    	$query = "SELECT
    		     u.subdomain sale_site
    		     , u.firstname
    			 , u.lastname
    			 , u.phone
    			 , u.phone2
    			 , u.email
    			 , u.company_name
    			 , u.address
    			 , u.address_2
    			 , u.city
    			 , u.state
    			 , u.zip
    			 , u.country
    			 , u.id member_id
    			 , FROM_UNIXTIME(u.date_joined, \"%M %e, %Y\") signup_date
    			 , u.referrer
				 FROM
			     geodesic_userdata u
    		     LEFT JOIN geodesic_user_groups_price_plans pp
        	     ON u.id = pp.id
			     WHERE 1 = 1
    				AND pp.group_id = 1
    				AND u.level = 0
                    AND u.subdomain = '".$_SERVER['HTTP_HOST']."'
			     ORDER BY u.id DESC";

    	$result = $db->Execute($query);
        if($result)
        {
        	if($result->RecordCount() > 0)
            {
            	$headline .= 'sale_site,';
                $headline .= 'firstname,';
                $headline .= 'lastname,';
                $headline .= 'phone,';
                $headline .= 'phone2,';
                $headline .= 'email,';
                $headline .= 'company_name,';
                $headline .= 'address,';
                $headline .= 'address_2,';
                $headline .= 'city,';
                $headline .= 'state,';
                $headline .= 'zip,';
                $headline .= 'country,';
                $headline .= 'member_id,';
                $headline .= 'signup_date,';
                $headline .= 'referrer'."\n";
                fwrite($file, $headline, 4096);

            	while($row = $result->FetchRow())
                {
                	$string .= $row['sale_site'].",";
                    $string .= $row['firstname'].",";
                    $string .= $row['lastname'].",";
                    $string .= $row['phone'].",";
                    $string .= $row['phone2'].",";
                    $string .= $row['email'].",";
                    $string .= $row['company_name'].",";
                    $string .= str_replace(",", "", $row['address']).",";
                    $string .= str_replace(",", "", $row['address_1']).",";
                    $string .= $row['city'].",";
                    $string .= $row['state'].",";
                    $string .= $row['zip'].",";
                    $string .= $row['country'].",";
                    $string .= $row['member_id'].",";
                    $string .= str_replace(",", " ", $row['signup_date']).",";
                    $string .= $row['referrer']."\n";

                    fwrite($file, $string, 4096);
                	$string = '';
                }
            	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            	header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            	header("Cache-Control: no-cache, must-revalidate");
            	header("Pragma: no-cache");
            	header("Content-Disposition: attachment; filename=$filename");

                readfile($filePath);
            	$pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
            	unlink($pathToFile);
            }
        	return(1);
        }
        else return(0);
    }
}
?>