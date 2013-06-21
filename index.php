<?php

    $printable					= 1;

    require_once( './config.php' );
    require_once( './classes/db_class.php' );
    require_once( './classes/controlpanel_class.php' );

    $conn						= new DB($db_host
									, $db_username
									, $db_password
									, $db_name
								);
    $conn->open();
    $cp							= new controlPanel( &$conn, $cpConfig );

    $a							= cbRequest( 'a' );
    $b							= cbRequest( 'b' );
    $c							= cbRequest( 'c' );
    $key						= cbRequest( 'key' );
    $act						= cbRequest( 'act' );
    $subdomain					= cbRequest( 'subdomain' );
    $file						= cbRequest( 'file' );
    $date						= cbRequest( 'start' );
    $type						= cbRequest( 'type' );

    if($a == 1)
    {
    	if($act == 'process') $str = process_add_site_info();
    	else $str = show_form_add_site();
    }
    elseif($a == 2)
    {
    	if($act == 'process') $str = process_add_landing_info();
    	else $str = show_form_add_landing();
    }
    elseif($a == 3)
    {
    	if($act == 'process') $str = process_add_sale_info();
    	else $str = show_form_add_sale();
    }
    elseif($a == 100 && !empty($b))
    {
    	if($act == 'process') $str = process_update_site_info($b);
        else $str = show_form_update_site($b);
    }
    elseif($a == 101 && !empty($b))
    {
        if($act == 'process' && !empty($c)) $str = process_delete_site_info($b,$c);
        elseif($act == 'false') $str = show_sites();
        else $str = show_form_delete_site($b,$c);
    }
    elseif($a == 102 && !empty($b))
    {
        if($act == 'process') $str = process_update_landing_info($b);
        else $str = show_form_update_landing($b);
    }
    elseif($a == 103 && !empty($b))
    {
        if($act == 'process') $str = process_delete_landing_info($b);
        elseif($act == 'false') $str = show_landings();
        else $str = show_form_delete_landing($b);
    }
    elseif($a == 104)
    {
    	if(!empty($b) && !empty($file) && !empty($key)) $str = generate_csv($b, $file, $key);
        elseif(!empty($b) && !empty($file)) $str = analise_dealer_datafeed_file($b, $file);
        elseif(!empty($b)) $str = show_form_select_available_feedfiles($b);
    	else $str = show_form_select_dealer();
    }
    elseif($a == 105)
    {
        if(!empty($subdomain)) $str = process_download_leads($subdomain, $date);
        else $str = show_form_download_leads();
    }
    elseif($a == 106)
    {           
        if($act == 'process') $str = $cp->process_download_leads_new($b, $type);        
        $str .= $cp->show_form_download_leads_new();
    }
    elseif($a == 110)
    {
    	if(!empty($b) && !empty($c) && $act == 'process') $str = process_create_maillist($b, $c);
        elseif(!empty($b) && !empty($c)) $str = show_form_maillist_create($b, $c);
    }
    else $str = 'Pick from left menus';

    $menu = show_left_cp();

    $template = implode("", file("templates/general.html"));
    $template = str_replace("@URL@", $cpUrl, $template);
    $template = str_replace("@LOGO@", $cpLogo, $template);
    $template = str_replace("@DATA@", $str, $template);
    $template = str_replace("@MENU@", $menu, $template);
    $template = str_replace("@BASEURL@", $cpUrl, $template);
    $template = str_replace("@TITLE@", $cpSiteName . ' Control Panel', $template);
        
    if($printable) echo $template;

//********************FUNCTIONS********************//
    function show_form_add_site()
    {
        global $conn;
        global $cp;

        $str = $cp->show_form_add_site();
        return $str;
    }

    function process_add_site_info()
    {
        global $conn;
        global $cp;

        $str = $cp->process_add_site_info();
        return $str;
    }

    function show_form_add_landing()
    {
        global $conn;
        global $cp;

         $str = $cp->show_form_add_landing();
        return $str;
    }

    function process_add_landing_info()
    {
        global $conn;
        global $cp;

        $str = $cp->process_add_landing_info();
        return $str;
    }

    function show_form_add_sale()
    {
        global $conn;
        global $cp;

         $str = $cp->show_form_add_sale();
        return $str;
    }

    function process_add_sale_info()
    {
        global $conn;
        global $cp;

        if(!$str = $cp->process_add_sale_info())
        {
        	$str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_sites()
    {
    	global $conn;
        global $cp;

        if(!$str = $cp->show_sites())
        {
        	$str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_landings()
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_landings())
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_left_cp()
    {
        global $conn;
        global $cp;

    	$str = show_sites();
        $str .= '<br>';
        $str .= show_landings();
        return $str;
    }

    function show_form_update_site($b)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_form_update_site($b))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_form_delete_landing($b)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_form_delete_landing($b))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function process_delete_landing_info($b)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->process_delete_landing_info($b))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_form_delete_site($b,$c)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_form_delete_site($b,$c))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function process_delete_site_info($b,$c)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->process_delete_site_info($b,$c))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function process_update_site_info($b)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->process_update_site_info($b))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_form_update_landing($b)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_form_update_landing($b))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function process_update_landing_info($b)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->process_update_landing_info($b))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_form_select_dealer()
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_form_select_dealer())
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_form_select_available_feedfiles($b)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_form_select_available_feedfiles($b))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function analise_dealer_datafeed_file($b, $file)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->analise_dealer_datafeed_file($b, $file))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function generate_csv($b, $c, $d)
    {
        global $conn;
        global $cp;

        if(!$str = $cp->generate_csv($b, $c, $d))
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function show_form_download_leads()
    {
        global $conn;
        global $cp;

        if(!$str = $cp->show_form_download_leads())
        {
            $str = 'Error: '.$cp->error;
        }
        return $str;
    }

    function process_download_leads($b, $date)
    {
        global $conn;
        global $cp;
        global $printable;

        if(!$str = $cp->process_download_leads($b, $date))
        {
            $str = 'Error: '.$cp->error;
            return true;
        }
        $printable = $cp->printable;
        return $str;
    }

    function show_form_maillist_create($b, $c)
    {
        global $conn;
        global $cp;
        global $printable;

        if(!$str = $cp->show_form_maillist_create($b, $c))
        {
            $str = 'Error: '.$cp->error;
            return $str;
        }
        return $str;
    }

    function process_create_maillist($b, $c)
    {
        global $conn;
        global $cp;
        global $printable;

        if(!$str = $cp->process_create_maillist($b, $c))
        {
            $str = 'Error: '.$cp->error;
            return $str;
        }
        return $str;
    }

//********************FUNCTIONS********************//
?>