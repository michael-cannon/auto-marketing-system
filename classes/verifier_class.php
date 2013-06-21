<?php
class verifier
{
    function verificate_date_data($data, $format)
    {
    	if($format == 'F d Y') $res = preg_match("#^[a-zA-Z]{3,10}[\s]{1}[0-9]{1,2}[\s]{1}[0-9]{4}$#", $data);
        return $res;
    }

    function verificate_int_data($data, $min=1, $max=4)
    {
    	$res = preg_match("#^[0-9]{1,4}$#", $data);
        return $res;
    }

    function verificate_mail_address_data($data)
    {
    	$valid = true;
    	$mails = explode(",", $data);
        for($i = 0; $i < count($mails); $i ++)
        {
        	$mail = trim($mails[$i]);
            if(!cbIsEmail($mail, false)) {
				$valid = false;
			}
        }
        if(!$valid) return false;
        else return true;
    }
}
?>