<?php
/**
 * Sendmail program
 * Automotive Marketing System
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: sendmail_class.php,v 1.1.1.1 2010/04/15 09:42:34 peimic.comprock Exp $
 */

class Sendmail
{
    var $conn = NULL;
    var $conn1 = NULL;
    var $error = null;
    var $errno = null;
    var $i = 0;
    var $messages = Array();
    var $errors = Array();
    var $accounts = Array();
    var $root = '/lists/';
    var $from = 'email@example.com';
    var $debug = 0;
    var $report = Array();
    var $conf;
    var $leadsFilename;
    var $domain;

    function Sendmail(&$conn, &$conn1, $conf)
    {
        $this->conn = $conn;
        $this->conn1 = $conn1;
		$this->conf = $conf;
		$this->root				= dirname( dirname( __FILE__ ) ) . $this->root;
		$this->from				= $this->conf['email'];
		$this->leadsFilename	= $this->conf['leadsFilename'] . '-';
		$this->domain			= $this->conf['postfix'];
    }

    function select_accounts()
    {
        $h_curr = date("H");
        $m_curr = date("i");

		// maillist times are quarterly
		switch( true )
		{
			case $m_curr <= 14:
				$m_curr	= 0;
				break;
			case $m_curr <= 29:
				$m_curr	= 15;
				break;
			case $m_curr <= 44:
				$m_curr	= 30;
				break;
			case $m_curr < 59:
				$m_curr	= 45;
				break;
		}
		// MLC force particular time
		// $h_curr	= 8;
		// $m_curr	= 0;

		$time	= time();

        $query = "SELECT
        m.id,
        m.domain,
        m.type,
        m.subject,
        m.message,
        m.contactsto,
        m.contactscc,
        m.contactsbcc,
        m.`interval`,
        t.use_pdf

        FROM maillist m LEFT JOIN times t
        ON m.id = t.maillistid
        WHERE m.active = 1
            AND m.datefrom <= $time
            AND m.dateto > $time
            AND t.hour = ".$h_curr."
            AND t.minutes = ".$m_curr.
		"
		"
		;

        if($this->debug) echo $query."<hr>";

        if(!$this->conn->query($query))
        {
            $this->error = 1;
            $this->errno = 12;
            return false;
        }

        $this->i = 0;
        while($row = $this->conn->fetchRow())
        {
            $this->accounts[$this->i]['id'] = $row['id'];
            $this->accounts[$this->i]['domain'] = $domain;
            $this->accounts[$this->i]['type'] = $row['type'];
            $this->accounts[$this->i]['subject'] = stripslashes(urldecode($row['subject']));
            $this->accounts[$this->i]['message'] = stripslashes(urldecode($row['message']));
            $this->accounts[$this->i]['contactsto'] = stripslashes(urldecode($row['contactsto']));
            $this->accounts[$this->i]['contactscc'] = stripslashes(urldecode($row['contactscc']));
            $this->accounts[$this->i]['contactsbcc'] = stripslashes(urldecode($row['contactsbcc']));
            $this->accounts[$this->i]['interval'] = $row['interval'];
            $this->accounts[$this->i]['use_pdf'] = $row['use_pdf'];

            if($row['type'] == 1)
            {
            	if(!$this->conn1->query("SELECT subdomain FROM sites WHERE id=".$row['domain']))
                {
            		$this->error = 1;
            		$this->errno = 13;
            		return false;
                }
                list($this->accounts[$this->i]['domain']) = $this->conn1->fetchRow();
            }
            elseif($row['type'] == 2)
            {
                if(!$this->conn1->query("SELECT domain FROM landingsites WHERE id=".$row['domain']))
                {
                    $this->error = 1;
                    $this->errno = 13;
                    return false;
                }
                list($this->accounts[$this->i]['domain']) = $this->conn1->fetchRow();
            }

			$domain  = $this->accounts[$this->i]['domain'];
			$domain  = stripslashes( urldecode($domain) );
			$domain  = parse_url( $domain );
			$domain  = ( isset( $domain['host'] ) )
						? $domain['host']
						: $domain['path'];
			$this->accounts[$this->i]['domain'] = $domain;

            $this->i ++;
        }

        return true;
    }

    function check_accounts()
    {
    	if($this->debug) echo "Debug. Step 1<br>";
        if(!$this->select_accounts())
        {
            $this->error = 1;
            $this->errno = 10;
            return false;
        }
        if($this->debug) echo "Debug. Step 2<br>";

        if($this->debug && $this->i > 0)
        {
            for($j = 0; $j < count($this->accounts); $j ++)
            {
                foreach($this->accounts[$j] as $key => $value)
                {
                    echo $key." => ".$value."<br>";
                }
            }
        }

        if($this->i > 0)
        {
        	for($i = 0; $i < $this->i; $i ++)
        	{
            	if(!$this->make_mail($i))
            	{
                	$this->error = 1;
                	//$this->errno = 6;
                	//return false;
            	}
            	if($this->accounts[$i]['use_pdf'] && $this->accounts[$i]['filecsv'])
            	{
                	if(!$this->make_pdf($i))
                	{
                    	$this->error = 1;
                    	$this->errno = 7;
                    	//return false;
                	}
            	}
            	if(!$this->send_mail($i))
            	{
                	$this->error = 1;
                	$this->errno = 8;
                	//return false;
            	}
                //Generage admin report for each maillist(mail, pdf)
        	}
        }

        return true;
    }

    function make_mail($i)
    {
        if(!$this->accounts[$i]['id'])
        {
            $this->error = 1;
            $this->errno = 2;
            return false;
        }

        if($this->accounts[$i]['type'] == 1)
        {
            if(!$this->make_maillist_leads($i))
            {
                $this->error = 1;
                $this->errno = 3;
                return false;
            }
        }
        elseif($this->accounts[$i]['type'] == 2)
        {
            if(!$this->make_maillist_sales($i))
            {
                $this->error = 1;
                $this->errno = 3;
                return false;
            }
        }
        return true;
    }

    function make_maillist_leads($i)
    {
        $num = 0;

        $query = "SELECT
                 u.subdomain sale_site
                 , u.firstname
                 , u.lastname
                 , u.phone
                 , u.phoneext
                 , u.phone2
                 , u.phoneext_2
                 , u.email
                 , u.company_name
                 , u.address
                 , u.address_2
                 , u.city
                 , u.state
                 , u.zip
                 , u.country
                 , u.id member_id
                 , FROM_UNIXTIME(u.date_joined, \"%M %e, %Y %H:%i\") signup_date
                 , u.referrer
                 FROM
                 geodesic_userdata u
                 LEFT JOIN geodesic_user_groups_price_plans pp
                 ON u.id = pp.id
                 WHERE 1 = 1
                    AND pp.group_id = 1
                    AND u.level = 0
                    AND u.subdomain LIKE '%{$this->accounts[$i]['domain']}{$this->domain}%'
                    AND unix_timestamp(SUBDATE(now(), INTERVAL ".$this->accounts[$i]['interval']." HOUR)) < u.date_joined

                 ORDER BY u.id DESC";

    	if($this->debug) echo $query."<hr color=#ff9900>";

        if($this->conn->query($query))
        {
            if($this->conn->numRows() > 0)
            {
                $salt = date(Ymd);
                $filename = $this->leadsFilename.strtoupper($this->accounts[$i]['domain']).'-'.$salt.'.csv';
                $filePath = $this->root.$filename;

                if(!$file = fopen($filePath, "w"))
                {
                	$this->error = 1;
                    $this->errno = 4;
                    return false;
                }

                $headline = 'sale_site,';
                $headline .= 'firstname,';
                $headline .= 'lastname,';
                $headline .= 'phone,';
                $headline .= 'phoneext,';
                $headline .= 'phone2,';
                $headline .= 'phoneext_2,';
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

                fwrite($file, $headline, 1024);

                $this->accounts[$i]['filecsv'] = $filePath;

                $r = 0;
                while($row = $this->conn->fetchRow())
                {
                    foreach($row as $rk => $rv)
                    {
                        $row[$rk] = preg_replace("#[\s]+#", ' ', $rv);
                    }

                    $string = trim(str_replace(",", "", $row['sale_site'])).",";
                    $string .= trim(str_replace(",", "", $row['firstname'])).",";
                    $string .= trim(str_replace(",", "", $row['lastname'])).",";
                    $string .= trim(str_replace(",", "", $row['phone'])).",";
                    $string .= trim(str_replace(",", "", $row['phoneext'])).",";
                    $string .= trim(str_replace(",", "", $row['phone2'])).",";
                    $string .= trim(str_replace(",", "", $row['phoneext_2'])).",";
                    $string .= trim(str_replace(",", "", $row['email'])).",";
                    $string .= trim(str_replace(",", "", $row['company_name'])).",";
                    $string .= trim(str_replace(",", "", $row['address'])).",";
                    $string .= trim(str_replace(",", "", $row['address_1'])).",";
                    $string .= trim(str_replace(",", "", $row['city'])).",";
                    $string .= trim(str_replace(",", "", $row['state'])).",";
                    $string .= trim(str_replace(",", "", $row['zip'])).",";
                    $string .= trim(str_replace(",", "", $row['country'])).",";
                    $string .= trim(str_replace(",", "", $row['member_id'])).",";
                    $string .= trim(str_replace(",", "", $row['signup_date'])).",";
                    $string .= trim(str_replace(",", "", $row['referrer']))."\n";

                    $this->report[$i][$r]['sale_site'] = trim(str_replace(",", "", $row['sale_site']));
                    $this->report[$i][$r]['firstname'] = trim(str_replace(",", "", $row['firstname']));
                    $this->report[$i][$r]['lastname'] = trim(str_replace(",", "", $row['lastname']));
                    $this->report[$i][$r]['phone'] = trim(str_replace(",", "", $row['phone']));
                    $this->report[$i][$r]['phoneext'] = trim(str_replace(",", "", $row['phoneext']));
                    $this->report[$i][$r]['phone2'] = trim(str_replace(",", "", $row['phone2']));
                    $this->report[$i][$r]['phoneext_2'] = trim(str_replace(",", "", $row['phoneext_2']));
                    $this->report[$i][$r]['email'] = trim(str_replace(",", "", $row['email']));
                    $this->report[$i][$r]['company_name'] = trim(str_replace(",", "", $row['company_name']));
                    $this->report[$i][$r]['address'] = trim(str_replace(",", "", $row['address']));
                    $this->report[$i][$r]['address_1'] = trim(str_replace(",", "", $row['address_1']));
                    $this->report[$i][$r]['city'] = trim(str_replace(",", "", $row['city']));
                    $this->report[$i][$r]['state'] = trim(str_replace(",", "", $row['state']));
                    $this->report[$i][$r]['zip'] = trim(str_replace(",", "", $row['zip']));
                    $this->report[$i][$r]['country'] = trim(str_replace(",", "", $row['country']));
                    $this->report[$i][$r]['member_id'] = trim(str_replace(",", "", $row['member_id']));
                    $this->report[$i][$r]['signup_date'] = trim(str_replace(",", "", $row['signup_date']));
                    $this->report[$i][$r]['referrer'] = trim(str_replace(",", "", $row['referrer']));

                    fwrite($file, $string, 1024);
                    $r ++;
                }
                fclose($file);
            }
        }
        else
        {
            $this->error = 1;
            $this->errno = 5;
            return false;
        }
        return true;
    }

    function make_maillist_sales($i)
    {
        $query = "SELECT
                 u.subdomain sale_site
                 , u.firstname
                 , u.lastname
                 , u.phone
                 , u.phoneext
                 , u.phone2
                 , u.phoneext_2
                 , u.email
                 , u.company_name
                 , u.address
                 , u.address_2
                 , u.city
                 , u.state
                 , u.zip
                 , u.country
                 , u.id member_id
                 , FROM_UNIXTIME(u.date_joined, \"%M %e, %Y %H:%i\") signup_date
                 , u.referrer
				 FROM
                 leads_userdata u
                 WHERE 1 = 1
                 AND u.subdomain IN ('http://".$this->accounts[$i]['domain']."/', 'http://www.".$this->accounts[$i]['domain']."/')
                 AND unix_timestamp(SUBDATE(now(), INTERVAL ".$this->accounts[$i]['interval']." HOUR)) < u.date_joined

                 ORDER BY u.id DESC";

		if($this->debug)
		{
			echo $query.'<hr>';
		}

        if($this->conn->query($query))
        {
        	if($this->conn->numRows() > 0)
            {
                $salt = date(Ymd);
                $filename = $this->leadsFilename.strtoupper($site).'-'.$salt.'.csv';
                $filePath = $this->root.$filename;

                if(!$file = fopen($filePath, "w"))
                {
                    $this->error = 1;
                    $this->errno = 4;
                    return false;
                }

                $headline = 'sale_site,';
                $headline .= 'firstname,';
                $headline .= 'lastname,';
                $headline .= 'phone,';
                $headline .= 'phoneext,';
                $headline .= 'phone2,';
                $headline .= 'phoneext_2,';
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

                fwrite($file, $headline, 1024);

                $r = 0;
                while($row = $this->conn->fetchRow())
                {
                	foreach($row as $rk => $rv)
                	{
	                	$row[$rk] = preg_replace("#[\s]+#", ' ', $rv);
                	}

                    $string = trim(str_replace(",", "", $row['sale_site'])).",";
                    $string .= trim(str_replace(",", "", $row['firstname'])).",";
                    $string .= trim(str_replace(",", "", $row['lastname'])).",";
                    $string .= trim(str_replace(",", "", $row['phone'])).",";
                    $string .= trim(str_replace(",", "", $row['phoneext'])).",";
                    $string .= trim(str_replace(",", "", $row['phone2'])).",";
                    $string .= trim(str_replace(",", "", $row['phoneext_2'])).",";
                    $string .= trim(str_replace(",", "", $row['email'])).",";
                    $string .= trim(str_replace(",", "", $row['company_name'])).",";
                    $string .= trim(str_replace(",", "", $row['address'])).",";
                    $string .= trim(str_replace(",", "", $row['address_1'])).",";
                    $string .= trim(str_replace(",", "", $row['city'])).",";
                    $string .= trim(str_replace(",", "", $row['state'])).",";
                    $string .= trim(str_replace(",", "", $row['zip'])).",";
                    $string .= trim(str_replace(",", "", $row['country'])).",";
                    $string .= trim(str_replace(",", "", $row['member_id'])).",";
                    $string .= trim(str_replace(",", "", $row['signup_date'])).",";
                    $string .= trim(str_replace(",", "", $row['referrer']))."\n";

                    $this->report[$i][$r]['sale_site'] = trim(str_replace(",", "", $row['sale_site']));
                    $this->report[$i][$r]['firstname'] = trim(str_replace(",", "", $row['firstname']));
                    $this->report[$i][$r]['lastname'] = trim(str_replace(",", "", $row['lastname']));
                    $this->report[$i][$r]['phone'] = trim(str_replace(",", "", $row['phone']));
                    $this->report[$i][$r]['phoneext'] = trim(str_replace(",", "", $row['phoneext']));
                    $this->report[$i][$r]['phone2'] = trim(str_replace(",", "", $row['phone2']));
                    $this->report[$i][$r]['phoneext_2'] = trim(str_replace(",", "", $row['phoneext_2']));
                    $this->report[$i][$r]['email'] = trim(str_replace(",", "", $row['email']));
                    $this->report[$i][$r]['company_name'] = trim(str_replace(",", "", $row['company_name']));
                    $this->report[$i][$r]['address'] = trim(str_replace(",", "", $row['address']));
                    $this->report[$i][$r]['address_1'] = trim(str_replace(",", "", $row['address_1']));
                    $this->report[$i][$r]['city'] = trim(str_replace(",", "", $row['city']));
                    $this->report[$i][$r]['state'] = trim(str_replace(",", "", $row['state']));
                    $this->report[$i][$r]['zip'] = trim(str_replace(",", "", $row['zip']));
                    $this->report[$i][$r]['country'] = trim(str_replace(",", "", $row['country']));
                    $this->report[$i][$r]['member_id'] = trim(str_replace(",", "", $row['member_id']));
                    $this->report[$i][$r]['signup_date'] = trim(str_replace(",", "", $row['signup_date']));
                    $this->report[$i][$r]['referrer'] = trim(str_replace(",", "", $row['referrer']));
                    $r ++;
                	fwrite($file, $string, 1024);
            	}
            	fclose($file);
                $this->accounts[$i]['filecsv'] = $filePath;
            }
        }
        else
        {
            $this->error = 1;
            $this->errno = 5;
            return false;
        }
        return true;
    }

    function make_pdf($i)
    {
        //Open PDF file
        $page = pdf_new();
        /*  open new PDF file; insert a file name to create the PDF on disk */
        $salt = date(Ymd);
        if($this->accounts[$i]['type'] == 1)$filenamePDF = $this->leadsFilename.strtoupper($this->accounts[$i]['domain'])."-".$salt.".pdf";
        elseif($this->accounts[$i]['type'] == 2)$filenamePDF = $this->leadsFilename.strtoupper($this->accounts[$i]['domain'])."-".$salt.".pdf";
        $filePathPDF = $this->root.$filenamePDF;

        if(!PDF_open_file($page, $filePathPDF))
        {
            $this->error = 1;
            $this->errno = 9;
            return false;
        }

        PDF_set_info($page, "Creator", "sendmail.php");
        PDF_set_info($page, "Author", "Peimic LLC");
        PDF_set_info($page, "Title", $this->accounts[$i]['domain']);
        PDF_set_info($page, "Subject", "Leads List");

        for($m = 0; $m < count($this->report[$i]); $m ++)
        {
            if(!($m%3) || !$m)
            {
                PDF_begin_page($page, 595, 842);
                $font = PDF_findfont($page, "Times-Roman", "host", 0); // declare default font
                PDF_setfont($page, $font, 10);
                PDF_set_text_pos($page, 50, 800);
                PDF_continue_text($page, "-----   File: ".basename($filePathPDF)."   -----");
                PDF_continue_text($page, "-----   Movingiron.com. Date of generat: ".date("F d Y"));
                PDF_continue_text($page, "-----   ".strtoupper($this->accounts[$i]['domain']));
            	PDF_continue_text($page, "\n");
            	PDF_continue_text($page, "\n");
            }
            PDF_continue_text($page, "-----   USER ".($m + 1)."   -----");
            PDF_continue_text($page, "**************************************************");
            foreach($this->report[$i][$m] as $key => $value)
            {
                $str = '';
                for($l = 0; $l < (80-2*strlen($key)); $l ++) $str .= ".";
                PDF_continue_text($page, $key.$str.$value);
            }
            PDF_continue_text($page, "**************************************************");
            PDF_continue_text($page, "\n");
            PDF_continue_text($page, "\n");
        	if(!(($m + 1)%3))
            {
                PDF_end_page($page);
            }
        }
        if((($m)%3)) PDF_end_page($page);
        PDF_close($page);

        $this->accounts[$i]['filepdf'] = $filePathPDF;
        return true;
    }

    function send_mail($i)
    {
        $to = $this->accounts[$i]['contactsto'];
        $subject = $this->accounts[$i]['subject'];
        if(!$this->accounts[$i]['filecsv']) {
          return true;                    
          //BCS-IT 29/09/06 if no leads, don't send email
          //  if($this->accounts[$i]['type'] == 1) $message = "There are no leads for ".strtoupper($this->accounts[$i]['domain'])."\n";
          //  elseif($this->accounts[$i]['type'] == 2) $message = "Call list for ".strtoupper($this->accounts[$i]['domain'])." is empty\n";
          //  $message .= "Interval: ".$this->accounts[$i]['interval']." h.\n";
        }
        else $message = $this->accounts[$i]['message'];

        $un        = strtoupper(uniqid(time()));

        $head = "From: ".$this->from."\n";

		// MLC 20070913 clean up
		if ( '' != $this->accounts[$i]['contactscc'] )
		{
        	$head .= "Cc: ".$this->accounts[$i]['contactscc']."\n";
		}

		if ( '' != $this->accounts[$i]['contactsbcc'] )
		{
        	$head .= "Bcc: ".$this->accounts[$i]['contactsbcc']."\n";
		}

       	// $head .= "Bcc: test@peimic.com\n";

        $head .= "X-Mailer: PHPMail Tool\n";
        $head .= "Reply-To: ".$this->from."\n";
        $head .= "Mime-Version: 1.0\n";
        $head .= "Content-Type:multipart/mixed;";
        $head .= "boundary=\"----------".$un."\"\n\n";

        $zag = "------------".$un."\nContent-Type:text/html;\n";
        $zag .= "Content-Transfer-Encoding: 8bit\n\n".$message."\n\n";

        if($this->accounts[$i]['filecsv'])
        {
            $f = fopen($this->accounts[$i]['filecsv'],"rb");
            $zag .= "------------".$un."\n";
            $zag .= "Content-Type: application/octet-stream;";
            $zag .= "name=\"".basename($this->accounts[$i]['filecsv'])."\"\n";
            $zag .= "Content-Transfer-Encoding:base64\n";
            $zag .= "Content-Disposition:attachment;";
            $zag .= "filename=\"".basename($this->accounts[$i]['filecsv'])."\"\n\n";
            $zag .= chunk_split(base64_encode(fread($f,filesize($this->accounts[$i]['filecsv']))))."\n";
            $zag .= "------------".$un."\n";
        }

        if($this->accounts[$i]['filepdf'])
        {
               $f = fopen($this->accounts[$i]['filepdf'],"rb");
            $zag .= "------------".$un."\n";
            $zag .= "Content-Type: application/octet-stream;";
            $zag .= "name=\"".basename($this->accounts[$i]['filepdf'])."\"\n";
            $zag .= "Content-Transfer-Encoding:base64\n";
            $zag .= "Content-Disposition:attachment;";
            $zag .= "filename=\"".basename($this->accounts[$i]['filepdf'])."\"\n\n";
            $zag .= chunk_split(base64_encode(fread($f,filesize($this->accounts[$i]['filepdf']))))."\n";
            $zag .= "------------".$un."\n";
        }

        if($this->debug)
        {
            echo "<br><strong>Mail parameters</strong><hr color=#ff9900
			size=1><pre>";
            echo "<br>Head:";
            echo "<br>".$head;
            echo "<br>To: ".$to;
            echo "<br>Subject: ".$subject;
            echo "<br>Zag: ".$zag;
            echo "</pre><hr color=#ff9900 size=1>";
        }

		mail($to, $subject, $zag, $head);

        if($this->accounts[$i]['filecsv']) unlink($this->accounts[$i]['filecsv']);
        if($this->accounts[$i]['filepdf']) unlink($this->accounts[$i]['filepdf']);

        return true;
    }
}
?>
