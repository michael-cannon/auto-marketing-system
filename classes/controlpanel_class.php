<?php

class controlPanel
{
    var $conn = NULL;
    var $i = 0;
    var $j = 0;
    var $k = 0;
    var $sites = Array();
    var $landings = Array();
    var $sales = Array();
    var $str = '';
    var $debug = 0;
    var $prefix = '';
    var $pathToTemplates = Array();
    var $pathToUploadFolder = '/dealer/enhanced/';
    var $uploadFolderPostfix = '_u';
    var $vehicles_file = 'VEHICLES.TXT';
    var $lotdata_file = 'LOTDATA.TXT';
    var $options_file = 'LINKS.TXT';
    var $vehicles_file_temp = 'VEHICLES_TEMP.TXT';
    var $lotdata_file_temp = 'LOTDATA_TEMP.TXT';
    var $options_file_temp = 'LINKS_TEMP.TXT';
    var $DOCUMENT_ROOT = '';
    var $interval_def = 24;
    var $printable = 1;

    var $postfix = '';
    var $mi_host = '';
    var $mi_login = '';
    var $mi_password = '';
    var $pathToCpFolder = '';
    var $pathToCsvFolder = '';

    var $bosy;
    var $conf				 = array();

    var $error = 0;
    var $errors = Array();

    function controlPanel( &$conn, $config )
    {
        $this->conn = $conn;
        $this->conf = $config;

		$this->postfix			= $config[ 'postfix' ];
		$this->mi_host			= $config[ 'mi_host' ];
		$this->mi_login			= $config[ 'mi_login' ];
		$this->mi_password		= $config[ 'mi_password' ];
		$this->pathToCpFolder	= $config[ 'pathToCpFolder' ];
		$this->pathToCsvFolder	= $config[ 'pathToCsvFolder' ];
        $this->pathToTemplates[1] = $config[ 'pathToTemplates[1]' ];
        $this->pathToTemplates[2] = $config[ 'pathToTemplates[2]' ];
        $this->pathToTemplates[3] = $config[ 'pathToTemplates[3]' ];

        $this->error_messages['contactsto'] = "Please insert a valid To emails";
        $this->error_messages['contactscc'] = "Please insert a valid Cc emails";
        $this->error_messages['contactsbcc'] = "Please insert a valid Bcc emails";
        $this->error_messages['interval'] = "Please insert a valid interval between 1-9999";
        $this->error_messages['datefrom'] = "Please insert a datefrom as FullMonthName(F) d Y";
        $this->error_messages['dateto'] = "Please insert a dateto as FullMonthName(F) d Y";
        $this->error_messages['subject'] = "Please insert a Subject";
        $this->error_messages['message'] = "Please insert a Message";
        $this->error_messages['timenum'] = "Please select time";

        $this->DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
    }

    function selectSites($id = 0)
    {
        if($id) $query = "SELECT * FROM sites WHERE id = ".$id." ORDER BY id DESC";
        else $query = "SELECT * FROM sites WHERE deleted = 0 ORDER BY id DESC";

        if(!$this->conn->query($query))
        {
            echo $this->conn->error;
        }
        else
        {
            $this->i = 0;
            while($row = $this->conn->fetchRow())
            {
                $this->sites[$this->i]['id'] = stripslashes(urldecode($row['id']));
                $this->sites[$this->i]['subdomain'] = stripslashes(urldecode($row['subdomain']));
                $this->sites[$this->i]['dealerusername'] = stripslashes(urldecode($row['dealerusername']));
                $this->sites[$this->i]['dealerpassword'] = stripslashes(urldecode($row['dealerpassword']));
                $this->sites[$this->i]['dealername'] = stripslashes(urldecode($row['dealername']));
                $this->sites[$this->i]['dealeraddress'] = stripslashes(urldecode($row['dealeraddress']));
                $this->sites[$this->i]['dealercity'] = stripslashes(urldecode($row['dealercity']));
                $this->sites[$this->i]['dealerstate'] = stripslashes(urldecode($row['dealerstate']));
                $this->sites[$this->i]['dealerpostcode'] = stripslashes(urldecode($row['dealerpostcode']));
                $this->sites[$this->i]['dealerwebsite'] = stripslashes(urldecode($row['dealerwebsite']));
                $this->sites[$this->i]['dealerheaderlogo'] = stripslashes(urldecode($row['dealerheaderlogo']));
                $this->sites[$this->i]['landingdomain'] = stripslashes(urldecode($row['landingdomain']));
                $this->sites[$htis->i]['emailsendregistrationto'] = stripslashes(urldecode($row['emailsendregistrationto']));
                $this->sites[$this->i]['inventorypocname'] = stripslashes(urldecode($row['inventorypocname']));
                $this->sites[$this->i]['inventoryocemail'] = stripslashes(urldecode($row['inventoryocemail']));
                $this->sites[$this->i]['inventorypocphone'] = stripslashes(urldecode($row['inventorypocphone']));
                $this->sites[$this->i]['specialinstructions'] = stripslashes(urldecode($row['specialinstructions']));
                $this->sites[$this->i]['datecreate'] = stripslashes(urldecode($row['datecreate']));
                $this->sites[$this->i]['datemodify'] = stripslashes(urldecode($row['datemodify']));
                $this->sites[$this->i]['live'] = stripslashes(urldecode($row['live']));
                $this->sites[$this->i]['datestart'] = stripslashes(urldecode($row['datestart']));
                $this->sites[$this->i]['dateend'] = stripslashes(urldecode($row['dateend']));
                $this->sites[$this->i]['additionalemailsto'] = stripslashes(urldecode($row['additionalemailsto']));
                $this->sites[$this->i]['additionalemailscc'] = stripslashes(urldecode($row['additionalemailscc']));

                $this->i ++;
                if($this->debug) echo "<hr color=#ff990>".$this->i."<ht color=#ff9900>";
            }
        }
        return($this->sites);
    }

    function selectLandings($id = 0)
    {
        if($id) $query = "SELECT * FROM landingsites WHERE id = ".$id." ORDER BY id DESC";
        else $query = "SELECT * FROM landingsites WHERE deleted = 0 ORDER BY id DESC";

        if(!$this->conn->query($query))
        {
            echo $this->conn->error;
        }
        else
        {
            $this->j = 0;
            while($row = $this->conn->fetchRow())
            {
				$domain  = $row['domain'];
				$domain  = stripslashes(urldecode($domain));
				$domain  = parse_url( $domain );
				$domain  = ( isset( $domain['host'] ) )
							? $domain['host']
							: $domain['path'];
                $this->landings[$this->j]['id'] = stripslashes(urldecode($row['id']));
                $this->landings[$this->j]['title'] = stripslashes(urldecode($row['title']));
                $this->landings[$this->j]['domain'] = $domain;
                $this->landings[$this->j]['datecreate'] = stripslashes(urldecode($row['datecreate']));
                $this->landings[$this->j]['datemodify'] = stripslashes(urldecode($row['datemodify']));
                $this->landings[$this->j]['host'] = stripslashes(urldecode($row['host']));
                $this->landings[$this->j]['login'] = stripslashes(urldecode($row['login']));
                $this->landings[$this->j]['password'] = stripslashes(urldecode($row['password']));
                $this->landings[$this->j]['live'] = stripslashes(urldecode($row['live']));

                $this->j ++;
                if($this->debug) echo "<hr color=#ff990>".$this->j."<ht color=#ff9900>";
            }
        }
        return($this->landings);
    }

    function selectSales($id = 0)
    {
        if($id) $query = "SELECT * FROM sales WHERE id = ".$id." ORDER BY id DESC";
        else $query = "SELECT * FROM sales ORDER BY id DESC";

        if(!$this->conn->query($query))
        {
            echo $this->conn->error;
        }
        else
        {
            $this->k = 0;
            while($row = $this->conn->fetchRow())
            {
                $this->sales[$this->k]['id'] = stripslashes(urldecode($row['id']));
                $this->sales[$this->k]['datestart'] = stripslashes(urldecode($row['datestart']));
                $this->sales[$this->k]['dateconvert'] = stripslashes(urldecode($row['dateconvert']));
                $this->sales[$this->k]['dateend'] = stripslashes(urldecode($row['dateend']));
                $this->sales[$this->k]['sale_type'] = stripslashes(urldecode($row['sale_type']));
                $this->sales[$this->k]['videofile_1_1'] = stripslashes(urldecode($row['videofile_1_1']));
                $this->sales[$this->k]['videofile_1_2'] = stripslashes(urldecode($row['videofile_1_2']));
                $this->sales[$this->k]['videofile_2_1'] = stripslashes(urldecode($row['videofile_2_1']));
                $this->sales[$this->k]['videofile_2_2'] = stripslashes(urldecode($row['videofile_2_2']));
                $this->sales[$this->k]['inventory_type'] = stripslashes(urldecode($row['inventory_type']));
                $this->sales[$this->k]['inventory_csvfile'] = stripslashes(urldecode($row['inventory_csvfile']));
                $this->sales[$this->k]['inventory_dbfiles'] = stripslashes(urldecode($row['inventory_dbfiles']));
                $this->sales[$this->k]['subdomain'] = stripslashes(urldecode($row['subdomain']));
                $this->sales[$this->k]['landingsitename'] = stripslashes(urldecode($row['landingsitename']));
                $this->sales[$this->k]['saletitle'] = stripslashes(urldecode($row['siletitle']));
                $this->sales[$this->k]['datecreatelanidngsite'] = stripslashes(urldecode($row['datecreatelanidngsite']));
                $this->sales[$this->k]['dateinventory'] = stripslashes(urldecode($row['dateinventory']));
                $this->sales[$this->k]['live'] = stripslashes(urldecode($row['live']));

                $this->k ++;
            }
        }
        return($this->sales);
    }

    function show_sites()
    {
        $this->body = '';
        $this->selectSites();
        if($this->i)
        {
            $this->body .= '<table width=100% bgcolor=#ccccff cellpadding=5 cellspacing=1 border=0>';
            $this->body .= '<tr><th align=center valign=middle>Sites<strong></strong></th></tr><tr><td bgcolor=#dedeff>';
            for($i = 0; $i < $this->i; $i ++)
            {
				$domain		= $this->sites[$i]['subdomain'].$this->postfix;
				$username	= $this->sites[$i]['dealerusername'];
				$password	= $this->sites[$i]['dealerpassword'];
                $this->body .= '<strong>';
                $this->body .= $this->sites[$i]['dealername'];
                $this->body .= '</strong><br><span class=menu_info_text>';
                $this->body .= '<a href="http://'.$domain;
                $this->body .= "/index.php?a=10&b[username]=$username&b[password]2=$password";
                $this->body .= '" target="_mi">';
                $this->body .= $domain.'</a> ';
                $this->body .= '<a href="http://'.$domain;
                $this->body .= "/admin/index.php?a=10&b[username]=miadmin&b[password]2=H0iB6k";
                $this->body .= '" target="_mi">';
                $this->body .= 'admin</a><br />';
                $this->body .= $this->sites[$i]['dealeraddress'].', ';
                $this->body .= $this->sites[$i]['dealercity'].', ';
                $this->body .= $this->sites[$i]['dealerstate'].' ';
                $this->body .= $this->sites[$i]['dealerpostcode'].' ';
                $this->body .= '</span><br>';
                $this->body .= '<a href=?a=100&b='.$this->sites[$i]['id'].'>Edit</a>';
                $this->body .= ' | ';
                $this->body .= '<a href=?a=101&b='.$this->sites[$i]['id'].'&c='.$this->sites[$i]['subdomain'].'>Del</a>';
                $this->body .= ' | ';
                $this->body .= '<a href=?a=110&b=1&c='.$this->sites[$i]['id'].'>Mailing List</a>';
                $this->body .= ' | ';
                $this->body .= '<a href=?a=106&b='.$this->sites[$i]['id'].'&type=site>Leads</a>';
                $this->body .= '<br />Inventory: ';
                $this->body .= '<a href=?a=104&b='.$this->sites[$i]['id'].'>DS</a>';
//                $this->body .= ' | ';
                $this->body .= '<hr color=#9999ff size=1 width=90% align=left>';
            }
            $this->body .= '</td></tr></table>';
        }
        else $this->body = 'There are no sites.';
        return $this->body;
    }

    function show_landings()
    {
        $this->body = '';
        $this->selectLandings();
        if($this->j)
        {
            $this->body .= '<table width=100% bgcolor=#ccccff cellpadding=5 cellspacing=1 border=0>';
            $this->body .= '<tr><th align=center valign=middle>Sale Sites<strong></strong></th></tr><tr><td bgcolor=#dedeff>';
            for($j = 0; $j < $this->j; $j ++)
            {
                $this->body .= '<strong>';
                $this->body .= $this->landings[$j]['title'].'</strong><br><span class=menu_info_text>';
                $this->body .= '<a href="http://';
                $this->body .= $this->landings[$j]['domain'] . '" target="_mi">';
                $this->body .= $this->landings[$j]['domain'] . '</a> Live: ';
                $this->body .= ( $this->landings[$j]['live'] ) ? 'Yes' : 'No';
                $this->body .= '</span><br>[<a href=?a=102&b='.$this->landings[$j]['id'].'>Edit</a>] [<a href=?a=103&b='.$this->landings[$j]['id'].'>Del</a>]';
                $this->body .= ' [<a href="?a=106&b=' . $this->landings[$j]['id'] . '&type=sale">Download leads</a>]';
                $this->body .= '<br>[<a href=?a=110&b=2&c='.$this->landings[$j]['id'].'>Edit Mailing List</a>]';
                $this->body .= '<hr color=#9999ff size=1 width=90% align=left>';
            }
            $this->body .= '</td></tr></table>';
        }
        else $this->body = 'There are no landings.';
        return $this->body;
    }

    function show_form_update_site($id)
    {
        $this->selectSites($id);

        $this->body = "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->body .= "<form name=addSiteInfo action=?a=100&b=".$id."&act=process method=post>";
        $this->body .= "<tr><th colspan=2><strong>Update site information</strong></th></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Subdomain</td>";
        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=subdomain value='".$this->sites[0]['subdomain']."'></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealer Username</td>";
        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerusername value='".$this->sites[0]['dealerusername']."'></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealer Password</td>";
        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerpassword value='".$this->sites[0]['dealerpassword']."'></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealername</td>";
        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealername value='".$this->sites[0]['dealername']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealeraddress</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=dealeraddress value='".$this->sites[0]['dealeraddress']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealercity</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=dealercity value='".$this->sites[0]['dealercity']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerstate</td><td bgcolor=#fafafa width=50% align=left><input size='2' name=dealerstate value='".$this->sites[0]['dealerstate']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerpostcode</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerpostcode value='".$this->sites[0]['dealerpostcode']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerwebsite</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerwebsite value='".$this->sites[0]['dealerwebsite']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerheaderlogo</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerheaderlogo value='".$this->sites[0]['dealerheaderlogo']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Landingdomain</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=landingdomain value='".$this->sites[0]['landingdomain']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Emailsendregistrationto</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=emailsendregistrationto value='".$this->sites[0]['emailsendregistrationto']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventorypocname</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=inventorypocname value='".$this->sites[0]['inventorypocname']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventoryocemail</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=inventoryocemail value='".$this->sites[0]['inventorypocemail']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventorypocphone</td><td bgcolor=#fafafa width=50% align=left><input size='30' name=inventorypocphone value='".$this->sites[0]['inventorypocphone']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Specialinstructions</td><td bgcolor=#fafafa width=50% align=left><textarea name=specialinstructions cols=30 rows=10>".$this->sites[0]['specialinstructions']."</textarea></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Additional Emails (To)</td><td bgcolor=#fafafa width=50% align=left><textarea name=additionalemailsto cols=30 rows=10>".$this->sites[0]['additionalemailsto']."</textarea></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Additional Emails (Cc)</td><td bgcolor=#fafafa width=50% align=left><textarea name=additionalemailscc cols=30 rows=10>".$this->sites[0]['additionalemailscc']."</textarea></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=center colspan=2><input type=submit value='>>> Update info >>>'></td></tr>";
        $this->body .= "</form>";
        $this->body .= "</table>";

        return $this->body;
    }

    function process_update_site_info($id)
    {
        $query = "UPDATE sites SET ";
        $query .= "subdomain = '".trim(addslashes(urlencode($_REQUEST['subdomain'])))."', ";
        $query .= "dealerpassword = '".trim(addslashes(urlencode($_REQUEST['dealerpassword'])))."', ";
        $query .= "dealerusername = '".trim(addslashes(urlencode($_REQUEST['dealerusername'])))."', ";
        $query .= "dealername = '".trim(addslashes(urlencode($_REQUEST['dealername'])))."', ";
        $query .= "dealeraddress = '".trim(addslashes(urlencode($_REQUEST['dealeraddress'])))."', ";
        $query .= "dealercity = '".trim(addslashes(urlencode($_REQUEST['dealercity'])))."', ";
        $query .= "dealerstate = '".trim(addslashes(urlencode($_REQUEST['dealerstate'])))."', ";
        $query .= "dealerpostcode = '".trim(addslashes(urlencode($_REQUEST['dealerpostcode'])))."', ";
        $query .= "dealerwebsite = '".trim(addslashes(urlencode($_REQUEST['dealerwebsite'])))."', ";
        $query .= "dealerheaderlogo = '".trim(addslashes(urlencode($_REQUEST['dealerheaderlogo'])))."', ";
        $query .= "landingdomain = '".trim(addslashes(urlencode($_REQUEST['landingdomain'])))."', ";
        $query .= "emailsendregistrationto = '".trim(addslashes(urlencode($_REQUEST['emailsendregistrationto'])))."', ";
        $query .= "inventorypocname = '".trim(addslashes(urlencode($_REQUEST['inventorypocname'])))."', ";
        $query .= "inventorypocemail = '".trim(addslashes(urlencode($_REQUEST['inventorypocemail'])))."', ";
        $query .= "inventorypocphone = '".trim(addslashes(urlencode($_REQUEST['inventorypocphone'])))."', ";
        $query .= "specialinstructions = '".trim(addslashes(urlencode($_REQUEST['specialinstructions'])))."', ";
        $query .= "additionalemailsto = '".trim(addslashes(urlencode($_REQUEST['additionalemailsto'])))."', ";
        $query .= "additionalemailscc = '".trim(addslashes(urlencode($_REQUEST['additionalemailscc'])))."', ";
        //$query .= "'".time()."', ";
        //$query .= "'".time()."', ";
        $query .= " live = '1' ";
        $query .= "WHERE id = ".$id." ";
        $query .= "LIMIT 1";

        if($this->debug) echo $query;

        if(!$this->conn->query($query))
        {
            //echo $this->conn->error;
            return($this->conn->error);
        }
        else return("Site info has been updated successfully.");
    }

    function show_form_update_landing($id)
    {
        $this->selectLandings($id);

        $this->body = "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->body .= "<form name=addLandingInfo action=?a=102&b=".$id."&act=process method=post>";
        $this->body .= "<tr><th colspan=2><strong>Update landing site information</strong></th></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Title</td>";
        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=title value='".$this->landings[0]['title']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Domain</td>";
        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=domain value='".$this->landings[0]['domain']."'></td></tr>";
//        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Host</td>";
//        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=host value='".$this->landings[0]['host']."'></td></tr>";
//        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Login</td>";
//        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=login value='".$this->landings[0]['login']."'></td></tr>";
//        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Password</td>";
//        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input size='30' name=password value='".$this->landings[0]['password']."'></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Live</td>";
        $checked				= ( $this->landings[0]['live'] )
									? " checked='checked'"
									: "";
        $this->body .= "<td bgcolor=#fafafa width=50% align=left><input type='checkbox' name=live value='1' $checked /></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=center colspan=2><input type=submit value='>>> Update info >>>'></td></tr>";
        $this->body .= "</form>";
        $this->body .= "</table>";

        return $this->body;
    }

    function process_update_landing_info($id)
    {
        $query = "UPDATE landingsites SET ";
        $query .= "title = '".trim(addslashes(urlencode($_REQUEST['title'])))."', ";
        $query .= "domain = '".trim(addslashes(urlencode($_REQUEST['domain'])))."', ";
        $query .= "host = '".trim(addslashes(urlencode($_REQUEST['host'])))."', ";
        $query .= "login = '".trim(addslashes(urlencode($_REQUEST['login'])))."', ";
        $query .= "password = '".trim(addslashes(urlencode($_REQUEST['password'])))."', ";
        $query .= "live = '".trim(addslashes(urlencode($_REQUEST['live'])))."' ";
        $query .= "WHERE id = ".$id;
        $query .= " LIMIT 1";

        if($this->debug) echo $query;

        if(!$this->conn->query($query))
        {
            //echo $this->conn->error;
            return($this->conn->error);
        }
        else return("Landing site info has been updated successfully.");
    }

    function show_form_add_site()
    {
        $this->str = "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->str .= "<form name=addSiteInfo action=?a=1&act=process method=post>";
        $this->str .= "<tr><th colspan=2><strong>Add site information</strong></th></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Subdomain<td bgcolor=#fafafa width=50% align=left><input size='30' name=subdomain value='".$_REQUEST['subdomain']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealername<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealername value='".$_REQUEST['dealername']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealeraddress<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealeraddress value='".$_REQUEST['dealeraddress']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealercity<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealercity value='".$_REQUEST['dealercity']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerstate<td bgcolor=#fafafa width=50% align=left><input size='2' name=dealerstate value='".$_REQUEST['dealerstate']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerpostcode<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerpostcode value='".$_REQUEST['dealerpostcode']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerwebsite<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerwebsite value='".$_REQUEST['dealerwebsite']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dealerheaderlogo<td bgcolor=#fafafa width=50% align=left><input size='30' name=dealerheaderlogo value='".$_REQUEST['dealerheaderlogo']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Landingdomain<td bgcolor=#fafafa width=50% align=left><input size='30' name=landingdomain value='".$_REQUEST['landingdomain']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Emailsendregistrationto<td bgcolor=#fafafa width=50% align=left><input size='30' name=emailsendregistrationto value='".$_REQUEST['emailsendregistrationto']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventorypocname<td bgcolor=#fafafa width=50% align=left><input size='30' name=inventorypocname value='".$_REQUEST['inventorypocname']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventoryocemail<td bgcolor=#fafafa width=50% align=left><input size='30' name=inventoryocemail value='".$_REQUEST['inventorypocemail']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventorypocphone<td bgcolor=#fafafa width=50% align=left><input size='30' name=inventorypocphone value='".$_REQUEST['inventorypocphone']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Specialinstructions<td bgcolor=#fafafa width=50% align=left><textarea name=specialinstructions cols=30 rows=10>".$_REQUEST['specialinstructions']."</textarea></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Additional Emails (To)<td bgcolor=#fafafa width=50% align=left><textarea name=additionalemailsto cols=30 rows=10>".$_REQUEST['additionalemailsto']."</textarea></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Additional Emails (Cc)<td bgcolor=#fafafa width=50% align=left><textarea name=additionalemailscc cols=30 rows=10>".$_REQUEST['additionalemailscc']."</textarea></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=center colspan=2><input type=submit value='>>> Add info >>>'></td></tr>";
        $this->str .= "</form>";
        $this->str .= "</table>";

        return $this->str;
    }

    function process_add_site_info()
    {
        $query = "INSERT INTO sites VALUES(
        '',
        '".trim(addslashes(urlencode($_REQUEST['subdomain'])))."',
        '".trim(addslashes(urlencode($_REQUEST['dealername'])))."',
        '".trim(addslashes(urlencode($_REQUEST['dealeraddress'])))."',
        '".trim(addslashes(urlencode($_REQUEST['dealercity'])))."',
        '".trim(addslashes(urlencode($_REQUEST['dealerstate'])))."',
        '".trim(addslashes(urlencode($_REQUEST['dealerpostcode'])))."',
        '".trim(addslashes(urlencode($_REQUEST['dealerwebsite'])))."',
        '".trim(addslashes(urlencode($_REQUEST['dealerheaderlogo'])))."',
        '".trim(addslashes(urlencode($_REQUEST['landingdomain'])))."',
        '".trim(addslashes(urlencode($_REQUEST['emailsendregistrationto'])))."',
        '".trim(addslashes(urlencode($_REQUEST['inventorypocname'])))."',
        '".trim(addslashes(urlencode($_REQUEST['inventorypocemail'])))."',
        '".trim(addslashes(urlencode($_REQUEST['inventorypocphone'])))."',
        '".trim(addslashes(urlencode($_REQUEST['specialinstructions'])))."',
        '".time()."',
        '".time()."',
        '1',
        '',
        '',
        '".trim(addslashes(urlencode($_REQUEST['additionalemailsto'])))."',
        '".trim(addslashes(urlencode($_REQUEST['additionalemailscc'])))."'
        )";

        if($this->debug) echo $query;

        if(!$this->conn->query($query))
        {
            //echo $this->conn->error;
            return($this->conn->error);
        }
        else return("Site hase been added successfully.");
    }

    function show_form_add_landing()
    {
        $this->str = "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->str .= "<form name=addLandingInfo action=?a=2&act=process method=post>";
        $this->str .= "<tr><th colspan=2><strong>Add landing site information</strong></th></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Title<td bgcolor=#fafafa width=50% align=left><input size='30' name=title value='".$_REQUEST['title']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Domain<td bgcolor=#fafafa width=50% align=left><input size='30' name=domain value='".$_REQUEST['domain']."'></td></td></tr>";
//        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Host<td bgcolor=#fafafa width=50% align=left><input size='30' name=host value='".$_REQUEST['host']."'></td></td></tr>";
//        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Login<td bgcolor=#fafafa width=50% align=left><input size='30' name=login value='".$_REQUEST['login']."'></td></td></tr>";
//        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Password<td bgcolor=#fafafa width=50% align=left><input size='30' name=password value='".$_REQUEST['password']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=center colspan=2><input type=submit value='>>> Add info >>>'></td></tr>";
        $this->str .= "</form>";
        $this->str .= "</table>";

        return $this->str;
    }

    function process_add_landing_info()
    {
        $query = "INSERT INTO landingsites VALUES(
			''
			, '".cbRequest('title')."'
			, '".cbRequest('domain')."'
			, '".time()."'
			, '".time()."'
			, '".cbRequest('host')."'
			, '".cbRequest('login')."'
			, '".cbRequest('password')."'
			, '1'
			, '0'
        )";

        if($this->debug) echo $query;

        if(!$this->conn->query($query))
        {
            return($this->conn->error);
        }
        else return("Landing site hase been added successfully.");
    }

    function show_form_add_sale()
    {
        $this->selectLandings();
        $this->selectSites();

        $types = Array('new/used' => 1, 'private_sale' => 2, 'fleet liquidation' => 3);

        $this->str = "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->str .= "<form name=addSaleInfo action=?a=3&act=process enctype='multipart/form-data' method=post>";
        $this->str .= "<tr><th colspan=2><strong>Add sale information</strong></th></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Datestart<td bgcolor=#fafafa width=50% align=left><input size='30' name=datestart value='".$_REQUEST['datestart']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dateconvert<td bgcolor=#fafafa width=50% align=left><input size='30' name=dateconvert value='".$_REQUEST['dateconvert']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Dateend<td bgcolor=#fafafa width=50% align=left><input size='30' name=dateend value='".$_REQUEST['dateend']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Saletype<td bgcolor=#fafafa width=50% align=left><select name=saletype>";

        foreach($types as $key => $value)
        {
            if($value == $_REQUEST['saletype']) $this->str .= "<option value=".$value." selected>".$key."</option>";
            else $this->str .= "<option value=".$value.">".$key."</option>";
        }

        $this->str .= "</td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Google map<td bgcolor=#fafafa width=50% align=left><input type=file name=map></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Video_1_1<td bgcolor=#fafafa width=50% align=left><input size='30' name=video_1_1 value='".$_REQUEST['video_1_1']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Video_1_2<td bgcolor=#fafafa width=50% align=left><input size='30' name=video_1_2 value='".$_REQUEST['video_1_2']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Video_2_1<td bgcolor=#fafafa width=50% align=left><input size='30' name=video_2_1 value='".$_REQUEST['video_2_1']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Video_2_2<td bgcolor=#fafafa width=50% align=left><input size='30' name=video_2_2 value='".$_REQUEST['video_2_2']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>ClearOverAll<td bgcolor=#fafafa width=50% align=left><input size='30' name=clearoverall value='".$_REQUEST['clearoverall']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventory type<td bgcolor=#fafafa width=50% align=left><input size='30' name=inventory_type value='".$_REQUEST['inventory_type']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventory CSV file<td bgcolor=#fafafa width=50% align=left><input size='30' name=inventory_csvfile value='".$_REQUEST['inventory_csvfile']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Inventory dbfiles<td bgcolor=#fafafa width=50% align=left><input size='30' name=inventory_dbfiles value='".$_REQUEST['inventory_dbfiles']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Subdomain<td bgcolor=#fafafa width=50% align=left><select name=subdomain>";

        for($i = 0; $i < $this->i; $i ++)
        {
            if($this->debug) echo "<hr>Sites: ".count($this->sites)."<hr>";

            if($this->sites[$i]['id'] == $_REQUEST['subdomain']) $this->str .= "<option value=".$this->sites[$i]['id']." selected>".$this->prefix.$this->sites[$i]['subdomain']."</option>";
            else $this->str .= "<option value=".$this->sites[$i]['id'].">".$this->sites[$i]['subdomain'].$this->postfix."</option>";
        }

        $this->str .= "</select></td></td></tr>";

        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Landingsitename<td bgcolor=#fafafa width=50% align=left><select name=landingsitename>";

        for($j = 0; $j < $this->j; $j ++)
        {
            if($this->debug) echo "<hr>Landings: ".count($this->landings)."<hr>";

            if($this->landings[$j]['id'] == $_REQUEST['landingsitename']) $this->str .= "<option value=".$this->landings[$j]['id']." selected>".$this->landings[$i]['domain']."</option>";
            else $this->str .= "<option value=".$this->landings[$j]['id'].">".$this->landings[$j]['domain']."</option>";
        }

        $this->str .= "</select></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Saletitle<td bgcolor=#fafafa width=50% align=left><input size='30' name=saletitle value='".$_REQUEST['saletitle']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=left>Live<td bgcolor=#fafafa width=50% align=left><input size='30' name=live value='".$_REQUEST['live']."'></td></td></tr>";
        $this->str .= "<tr><td bgcolor=#fafafa width=50% align=center colspan=2><input type=submit value='>>> Add info >>>'></td></tr>";
        $this->str .= "</form>";
        $this->str .= "</table>";

        return $this->str;
    }

    function show_form_select_dealer()
    {
        $this->selectSites();
        $this->body = '<table width=100% bgcolor=#ccccff cellpadding=5 cellspacing=1 border=0>';
        $this->body .= '<form name=select_dealers action=index.php?a=104 method=post>';
        $this->body .= '<tr><td bgcolor=#fafafa width=50% align=left>Subdomain</td>';
        $this->body .= '<td bgcolor=#fafafa width=50% align=left><select name=b>';

        for($i = 0; $i < $this->i; $i ++)
        {
            if($this->debug) echo '<hr>Sites: '.count($this->sites).'<hr>';

            if($this->sites[$i]['id'] == $_REQUEST['subdomain'])
            $this->body .= '<option value='.$this->sites[$i]['id'].' selected>'.$this->prefix.$this->sites[$i]['subdomain'].'</option>';
            else $this->body .= '<option value='.$this->sites[$i]['id'].'>'.$this->sites[$i]['subdomain'].$this->postfix.'</option>';
        }

        $this->body .= '</select></td></td></tr>';
        $this->body .= '<tr><td colspan=2 bgcolor=#fafafa width=100% align=center><input type=submit value=" >> Go >> "></td>';
        $this->body .= '</form>';
        $this->body .= '</table>';

        return $this->body;
    }

    function show_form_select_available_feedfiles($b)
    {
        $this->selectSites($b);
        $pathToFolder = $_SERVER['DOCUMENT_ROOT'].$this->pathToUploadFolder;

        if(is_dir($pathToFolder))
        {
            if(!$dir = opendir($pathToFolder))
            {
                $this->error = 13;
                return false;
            }
            $this->selectSites();
            $this->body = '<table width=100% bgcolor=#ccccff cellpadding=5 cellspacing=1 border=0>';
            $this->body .= '<form name=select_dealers action=index.php?a=104&b='.$b.' method=post>';
            $this->body .= '<tr><td bgcolor=#fafafa width=50% align=left>Available Files</td>';
            $this->body .= '<td bgcolor=#fafafa width=50% align=left>';
            $this->body .= '<select name=file>';
            $this->body .= '<option>------      Select File      ------</option>';
			$files = array();
            while($file = readdir($dir))
            {
                if(!is_dir($file))
                {
					// dealer specialties
                    if(preg_match('#\d{6}.zip#', $file))
                    {
						$files[] = $file;
                    }
                }
            }
			natsort($files);
			$files = array_reverse( $files );
			foreach($files as $key => $file)
			{
				$this->body .= '<option value="'.$file.'">';
				$this->body .= $file;
				$this->body .= '</option>';
			}
            $this->body .= '</select>';
            $this->body .= '</td></td></tr>';
            $this->body .= '<tr><td colspan=2 bgcolor=#fafafa width=100% align=center><input type=submit value=" >> Analyze File >> "></td>';
            $this->body .= '</form>';
            $this->body .= '</table>';
        }
        else $this->body = '<p>It looks like there are no files for this dealer.</p>';

        if($this->debug) echo 'Folder is '.$pathToFolder;

        return $this->body;
    }

    function analise_dealer_datafeed_file($b, $c)
    {
        $this->selectSites($b);
        $pathSourceFolder = $_SERVER['DOCUMENT_ROOT'].$this->pathToUploadFolder;
        $file = $pathSourceFolder.'/'.$c;
        $pathCopyToFolder = $_SERVER['DOCUMENT_ROOT'].$this->pathToCpFolder.$this->sites[0]['subdomain'];
        $destination_file = $pathCopyToFolder.'/'.$c;
        if(file_exists($file))
        {
            if(!is_dir($pathCopyToFolder))
            {
                if(!mkdir($pathCopyToFolder))
                {
                    $this->error = 16; //Can't make new folder $pathCopyTo
                    cbDebug( '$pathCopyToFolder', $pathCopyToFolder );
                    return false;
                }
            }
            if(!copy($file, $destination_file))
            {
                echo "$file => $destination_file";
                $this->error = 17; //Can't copy file
                return false;
            }
            if($this->debug) echo 'Info: '.$file.' From to '.$destination_file;

            $exec_str = 'unzip -o '.$destination_file.' -d '.$pathCopyToFolder;
            shell_exec($exec_str);

            if(!file_exists($pathCopyToFolder.'/'.$this->vehicles_file) ||
            !file_exists($pathCopyToFolder.'/'.$this->lotdata_file) ||
            !file_exists($pathCopyToFolder.'/'.$this->options_file))
            {
                $this->error = 18; // Incorrect datafeed files suit
                return false;
            }

            if(!$vehicle_file = fopen($pathCopyToFolder.'/'.$this->vehicles_file, "r"))
            {
                $this->error = 20; //Can't open file
                return false;
            }

            $file = fopen($pathCopyToFolder.'/'.$this->vehicles_file, "r");
            $file_temp = fopen($pathCopyToFolder.'/'.$this->vehicles_file_temp, "w+");
			while (!feof($file))
			{
                $str = fgets($file, 4096);
                $str = str_replace("\\", "@@SLASH@@", $str);
                fwrite($file_temp, $str);
            }
            rewind($file_temp);
            fclose($file_temp);

            $file_temp = fopen($pathCopyToFolder.'/'.$this->lotdata_file, "r");
			$keys = array();
			$count = 0;
            while(($inf = fgetcsv($file_temp, 50000, ",")) !== FALSE)
            {
				// $keys[$count]['DealerID'] = $inf[0];
				// $keys[$count]['DealerName'] = $inf[1];
				$keys[$inf[1]] = $inf[0];
				$count++;
            }
            fclose($file_temp);

			ksort($keys);

            $this->body = '<table width=100% bgcolor=#ccccff cellpadding=5 cellspacing=1 border=0>';
            $this->body .= '<form name=select_dealers action=index.php?a=104&b='.$b.'&file='.$c.' method=post>';
            $this->body .= '<tr><td bgcolor=#fafafa width=50% align=left>Available Dealer Keys</td>';
            $this->body .= '<td bgcolor=#fafafa width=50% align=left>';
            $this->body .= '<select name=key>';
            $this->body .= '<option>------      Select Vehicles Key      ------</option>';
            foreach($keys as $DealerName => $DealerID)
            {
                $this->body .= '<option value="'.$DealerID.'">';
                $this->body .= $DealerName.' - '.$DealerID;
                $this->body .= '</option>';
            }
            $this->body .= '</select>';
            $this->body .= '</td></td></tr>';
            $this->body .= '<tr><td colspan=2 bgcolor=#fafafa width=100% align=center><input type=submit value=" >> Generate CSV File >> "></td>';
            $this->body .= '</form>';
            $this->body .= '</table>';

            return $this->body;
        }
        else
        {
            $this->error = 15; //File $file not exists
            return false;
        }
    }

    function generate_csv($b, $c, $d)
    {
        $this->selectSites($b);
        $pathCopyToFolder = $_SERVER['DOCUMENT_ROOT'].$this->pathToCpFolder.$this->sites[0]['subdomain'];
        $pathToCsvFolder = $_SERVER['DOCUMENT_ROOT'].$this->pathToCsvFolder.$this->sites[0]['subdomain'];

        if(!$options_file = fopen($pathCopyToFolder.'/'.$this->options_file, "r"))
        {
            $this->error = 26;
            return false;
        }
        if(!$options_temp = fopen($pathCopyToFolder.'/'.$this->options_file_temp, "w+"))
        {
            $this->error = 27;
            return false;
        }
        while(!feof($options_file))
        {
            $str = fgets($options_file, 4096);
            $str = str_replace("\\", "@@SLASH@@", $str);
            fwrite($options_temp, $str);
        }
        fclose($options_file);

        rewind($options_temp);
        while(($data = fgetcsv($options_temp, 50000, ",")) !== FALSE)
        {
			// map vin to photourls
            $options[$data[1]] = strtolower($data[4]);
        }
        fclose($options_temp);

        if(!is_dir($pathToCsvFolder))
        {
            if(!mkdir($pathToCsvFolder))
            {
                $this->error = 16; //Can't make new folder $pathCopyTo
				cbDebug( 'pathToCsvFolder', $pathToCsvFolder );	
                return false;
            }
        }

        if(!$file_temp = fopen($pathCopyToFolder.'/'.$this->vehicles_file_temp, "r"))
        {
            $this->error = 23;
            return false;
        }
        $csvFile = $this->sites[0]['subdomain'].'-'.date("Y-M-d-H-i").'.csv';
        if(!$file_csv = fopen($pathToCsvFolder.'/'.$csvFile, "w"))
        {
            $this->error = 24;
            return false;
        }
        $csvVinsFile = $this->sites[0]['subdomain'].'-vins-'.date("Y-M-d-H-i").'.csv';
        if(!$file_csv_vins = fopen($pathToCsvFolder.'/'.$csvVinsFile, "w"))
        {
            $this->error = 25;
            return false;
        }

        $header = '"name","stock","usednew","vin","year","make","model","series","body","color","intcolor","price","miles","transmission","engine","comments","options","photourl","fuel","drivetrain","doors","interiorcolor","warranty"'."\n";

        if(!fwrite($file_csv, $header))
        {
            $this->error = 20; // Can't write to file
            return false;
        }

        rewind($file_temp);
        $k = 0;
        while(($data = fgetcsv($file_temp, 50000, ",")) !== FALSE)
        {
            if(count($data) == 63 && ($data[0] == $d || ($this->sites[0]['subdomain'] == 'sc')))
            {
                for($i = 0; $i < count($data); $i ++)
                {
                    $bu[$bukeys[$data[$i]]] = $values[$data[$i]];
                }

                if($this->debug)
                {
                    foreach($bu as $bk => $bv)
                    {
                        echo $bk.' => '.$bv.'<br>';
                    }
                }

				$bu['used'] = 'Used';
                $bu['year'] = $data[5];
                $bu['series'] = $data[8];
                $bu['body'] = $data[9];
                $bu['miles'] = $data[11];
                $bu['transmission'] = $data[12];
                $bu['engine'] = $data['17']." ".$data[13]." liter ".$data['14']." ".$data['15'];
                $bu['vin'] = $data[1];
                $vin = $data[1];
                $bu['stock'] = $data[2];
                $bu['extcolor'] = $data[23];
                $bu['intcolor'] = $data[24];
                $bu['make'] = $data[6];
                $bu['model'] = $data[7];
                $bu['price'] = $data[26];
                $bu['options'] = str_replace('|', ', ', $data[46]);
                $bu['warranty'] = $data[29];
                $bu['comments'] = $data[45];
                $bu['fuel'] = $data[17];
                $bu['drivetrain'] = $data[16];
                $bu['doors'] = $data[21];
                $bu['interiorcolor'] = $data[24];
                $bu['warranty'] = $data[29];

                $str_vins .= $data[1]."<br>";
                $k ++;
				$str			= '"'.$this->sites[0]['dealername']
					.'","'.$bu['stock']
					.'","'.$bu['used']
					.'","'.$bu['vin']
					.'","'.$bu['year']
					.'","'.$bu['make']
					.'","'.$bu['model']
					.'","'.$bu['series']
					.'","'.$bu['body']
					.'","'.$bu['extcolor']
					.'","'.$bu['intcolor']
					.'","'.$bu['price']
					.'","'.$bu['miles']
					.'","'.$bu['transmission']
					.'","'.$bu['engine']
					.'","'.$bu['comments']
					.'","'.$bu['options']
					.'","'.$options[$vin]
					.'","'.$bu['fuel']
					.'","'.$bu['drivetrain']
					.'","'.$bu['interiorcolor']
					.'","'.$bu['warranty']
					.'"'
					."\n";

                $vin .= "\n";

				$str = str_ireplace('Not Specified', '', $str);

                if($this->debug) echo $str.'<br>';
                if($this->debug) echo $vin.'<hr color=#ff9900 size=1>';

                if(!fwrite($file_csv, $str))
                {
                    $this->error = 21; // Can't write to csv file
                    return false;
                }
                if(!fwrite($file_csv_vins, $vin))
                {
                    $this->error = 22; // Can't write to csv_vins file
                    return false;
                }

                unset($bu);
            }
        }

        shell_exec('rm '.$pathCopyToFolder.'/*.txt');
        shell_exec('rm '.$pathCopyToFolder.'/*.TXT');

        $this->body = '<table width=100% bgcolor=#ccccff cellpadding=5 cellspacing=1 border=0>';
        $this->body .= '<tr><td bgcolor=#fafafa width=50% align=left>Correct CSV File</td>';
        $this->body .= '<td bgcolor=#fafafa width=50% align=left><a href='.$this->pathToCsvFolder.$this->sites[0]['subdomain'].'/'.$csvFile.'>'.$csvFile.'</a></td>';
        $this->body .= '</tr>';
        $this->body .= '<tr><td bgcolor=#fafafa width=50% align=left>VINs File</td>';
        $this->body .= '<td bgcolor=#fafafa width=50% align=left><a href='.$this->pathToCsvFolder.$this->sites[0]['subdomain'].'/'.$csvVinsFile.'>'.$csvVinsFile.'</a></td>';
        $this->body .= '</tr>';
        $this->body .= '<tr><td bgcolor=#fafafa width=50% align=left valign=top>VINs (Total: '.$k.')</td>';
        $this->body .= '<td bgcolor=#fafafa width=50% align=left valign=top>'.$str_vins.'</td>';
        $this->body .= '</tr>';
        $this->body .= '</table>';

        return $this->body;
    }

    function process_add_sale_info()
    {
        //COPY_FILES_FROM_MOVINGIRON_TEMPLATES_TO_LANDING_SITE
        $landinginfo = $this->selectLandings($_REQUEST['landingsitename']);
        $subdomain = $this->selectSites($_REQUEST['subdomain']);

        $ftp_mi_conn = ftp_connect($this->mi_host);
        if(!ftp_login($ftp_mi_conn, $this->mi_login.'@'.$this->mi_host, $this->mi_password))
        {
            $this->error = 1;
            return false;
        }

        $ftplogin = $landinginfo[0]['login'];
        $ftppassword = $landinginfo[0]['password'];
        $ftphost = $landinginfo[0]['host'];

        if($this->debug)
        {
            echo '<hr>ftphost: '.$ftphost.'<br>';
            echo '<hr>ftplogin: '.$ftplogin.'<br>';
            echo '<hr>ftppassword: '.$ftppassword.'<br>';
        }

        $ftpconn = ftp_connect($ftphost);
        if(!ftp_login($ftpconn, $ftplogin.'@'.$ftphost, $ftppassword))
        {
            if(!ftp_login($ftpconn, $ftplogin, $ftppassword))
            {
                $this->error = 1;
                return false;
            }
        }

        $dirs = ftp_nlist($ftpconn, '.');

        if($this->debug)
        {
            echo "DIR RESULT: ";
            echo '<br>Number of files is '.count($dirs);
            foreach($dirs as $nlist)
            {
                echo '<br>'.$nlist.'<br>';
            }
        }

        if(in_array('public_html', $dirs) || in_array('mail', $dirs) || in_array('public_ftp', $dirs) || in_array('.bashrc', $dirs))
        {
            $public_html = '/public_html';
            if($this->debug) echo '<hr color=#ff9900>public_html: '.$public_html;
        }

        $dirs = ftp_nlist($ftpconn, '.'.$public_html);

        if($this->debug)
        {
            echo "DIR RESULT: ";
            echo '<br>Number of files is '.count($dirs);
            foreach($dirs as $nlist)
            {
                echo '<br>'.$nlist.'<br>';
            }
        }

        if(!in_array('landing', $dirs) && $_REQUEST['saletype'] == 1)
        {
            if(!ftp_mkdir($ftpconn, '.'.$public_html.'/landing'))
            {
                $this->error = 2;
                return false;
            }
        }

        if(!$dir = opendir($this->pathToTemplates[$_REQUEST['saletype']]))
        {
            if($this->debug)
            {
                echo 'Saletype: '.$_REQUEST['saletype'];
                echo 'Dir1: '.$this->pathToTemplates[1];
                echo 'Dir2: '.$this->pathToTemplates[2];
                echo 'Dir3: '.$this->pathToTemplates[3];
                echo 'Dir: '.$this->pathToTemplates[$_REQUEST['saletype']];
            }
            $this->error = 3;
            return false;
        }

        while($file = readdir($dir))
        {
            if(!is_dir($this->pathToTemplates[$_REQUEST['saletype']].'/'.$file))
            {
                if($file != '.' && $file != '..' && $file != 'landing' && $file != 'img')
                {
                    if($this->debug) echo "<br>File: ".$file;
                    if($file != 'cb_common.tar' && $file != 'cb_cogs.tar')
                    {
                        if(!ftp_put($ftpconn, '.'.$public_html.'/'.$file, $this->pathToTemplates[$_REQUEST['saletype']].'/'.$file, FTP_ASCII))
                        {
                            $this->error = 4;
                            return false;
                        }
                    }
                    else
                    {
                        if(!ftp_put($ftpconn, '.'.$public_html.'/'.$file, $this->pathToTemplates[$_REQUEST['saletype']].'/'.$file, FTP_BINARY))
                        {
                            $this->error = 4;
                            return false;
                        }
                    }
                }
            }
        }
        closedir($dir);

        if(!$dir = opendir($this->pathToTemplates[$_REQUEST['saletype']].'/img/'))
        {
            if($this->debug)
            {
                echo 'Saletype: '.$_REQUEST['saletype'];
                echo 'Dir1: '.$this->pathToTemplates[1];
                echo 'Dir2: '.$this->pathToTemplates[2];
                echo 'Dir3: '.$this->pathToTemplates[3];
                echo 'Dir: '.$this->pathToTemplates[$_REQUEST['saletype']];
            }
            $this->error = 3;
            return false;
        }

        while($file = readdir($dir))
        {
            if(!is_dir($this->pathToTemplates[$_REQUEST['saletype']].'/img/'.$file))
            {
                if($file != '.' && $file != '..')
                {
                    if($this->debug) echo "<br>File: ".$file;
                    if(!ftp_put($ftpconn, '.'.$public_html.'/'.$file, $this->pathToTemplates[$_REQUEST['saletype']].'/img/'.$file, FTP_BINARY))
                    {
                        $this->error = 4;
                        return false;
                    }
                }
            }
        }
        closedir($dir);

        if($_REQUEST['saletype'] == 1)
        {
            if(!$dir = opendir($this->pathToTemplates[$_REQUEST['saletype']].'/landing/'))
            {
                $this->error = 3;
                return false;
            }

            while($file = readdir($dir))
            {
                if(!is_dir($this->pathToTemplates[$_REQUEST['saletype']].'/landing/'.$file))
                {
                    if($file != '.' && $file != '..')
                    {
                        if($this->debug) echo "<br>File: ".$file;
                        if(!ftp_put($ftpconn, '.'.$public_html.'/landing/'.$file, $this->pathToTemplates[$_REQUEST['saletype']].'/landing/'.$file, FTP_ASCII))
                        {
                            $this->error = 4;
                            return false;
                        }
                    }
                }
            }
            closedir($dir);
        }

        $videoFile_1_1 = ($_REQUEST['video_1_1']) ? trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/'.$_REQUEST['video_1_1'])) : trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$subdomain[0]['subdomain'].'/'.$subdomain[0]['subdomain'].'_1.swf'));
        $videoFile_1_2 = ($_REQUEST['video_1_2']) ? trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/'.$_REQUEST['video_1_2'])) : trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$subdomain[0]['subdomain'].'/'.$subdomain[0]['subdomain'].'_1.flv'));
        $videoFile_2_1 = ($_REQUEST['video_2_1']) ? trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/'.$_REQUEST['video_2_1'])) : trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$subdomain[0]['subdomain'].'/'.$subdomain[0]['subdomain'].'_2.swf'));
        $videoFile_2_2 = ($_REQUEST['video_2_2']) ? trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/'.$_REQUEST['video_2_2'])) : trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$subdomain[0]['subdomain'].'/'.$subdomain[0]['subdomain'].'_2.flv'));
        $clearOverAll =  ($_REQUEST['video_2_2']) ? trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/'.$_REQUEST['clearoverall'])) : trim(stripslashes($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$subdomain[0]['subdomain'].'/clearoverall.swf'));

        $BN_videoFile_1_1 = basename($videoFile_1_1);
        $BN_videoFile_1_2 = basename($videoFile_1_2);
        $BN_videoFile_2_1 = basename($videoFile_2_1);
        $BN_videoFile_2_2 = basename($videoFile_2_2);
        $BN_clearOverAll = basename($clearOverAll);

        if($this->debug)
        {
            echo '<hr>';
            echo $videoFile_1_1.'<br>';
            echo $videoFile_1_2.'<br>';
            echo $videoFile_2_1.'<br>';
            echo $videoFile_2_2.'<br>';
            echo $clearOverAll.'<br>';
            echo '<br>';
        }

        if(file_exists($videoFile_1_1))
        {
            if($this->debug) echo 'VideoFile_1_1: '.$videoFile_1_1.'<br>';
            if($_REQUEST['saletype'] == 1) if(!ftp_put($ftpconn, '.'.$public_html.'/landing/'.$BN_videoFile_1_1, $videoFile_1_1, FTP_BINARY)) {$this->error = 8; return false;}
            if(!ftp_put($ftpconn, '.'.$public_html.'/'.$BN_videoFile_1_1, $videoFile_1_1, FTP_BINARY)) {$this->error = 8; return false;}
        }
        elseif($this->debug) echo '<br>File not fount: '.$videoFile_1_1;
        if(file_exists($videoFile_1_2))
        {
            if($this->debug) echo 'VideoFile_1_2: '.$videoFile_1_2.'<br>';
            if($_REQUEST['saletype'] == 1) ftp_put($ftpconn, '.'.$public_html.'/landing/'.$BN_videoFile_1_2, $videoFile_1_2, FTP_BINARY);
            ftp_put($ftpconn, '.'.$public_html.'/'.$BN_videoFile_1_2, $videoFile_1_2, FTP_BINARY);
        }
        elseif($this->debug) echo '<br>File not fount: '.$videoFile_1_2;
        if(file_exists($videoFile_2_1))
        {
            if($this->debug) echo 'VideoFile_2_1: '.$videoFile_2_1.'<br>';
            if($_REQUEST['saletype'] == 1) ftp_put($ftpconn, '.'.$public_html.'/landing/'.$BN_videoFile_2_1, $videoFile_2_1, FTP_BINARY);
            ftp_put($ftpconn, '.'.$public_html.'/'.$BN_videoFile_2_1, $videoFile_2_1, FTP_BINARY);
        }
        elseif($this->debug) echo '<br>File not fount: '.$videoFile_2_1;
        if(file_exists($videoFile_2_2))
        {
            if($this->debug) echo 'VideoFile_2_2: '.$videoFile_2_2.'<br>';
            if($_REQUEST['saletype'] == 1) ftp_put($ftpconn, '.'.$public_html.'/landing/'.$BN_videoFile_2_2, $videoFile_2_2, FTP_BINARY);
            ftp_put($ftpconn, '.'.$public_html.'/'.$BN_videoFile_2_2, $videoFile_2_2, FTP_BINARY);
        }
        elseif($this->debug) echo '<br>File not fount: '.$videoFile_2_2;
        if(file_exists($clearOverAll))
        {
            if($this->debug) echo 'clearOverAll: '.$clearOverAll.'<br>';
            if($_REQUEST['saletype'] == 1) if(!ftp_put($ftpconn, '.'.$public_html.'/landing/'.$BN_clearOverAll, $clearOverAll, FTP_BINARY)) {$this->error = 8; return false;}
            if(!ftp_put($ftpconn, '.'.$public_html.'/'.$BN_clearOverAll, $clearOverAll, FTP_BINARY)) {$this->error = 8; return false;}
        }
        elseif($this->debug) echo '<br>File not fount: '.$clearOverAll;

        if($_REQUEST['saletype'] == 1)
        {
            if(!$file = implode("", file($this->pathToTemplates[$_REQUEST['saletype']].'/index.htm')))
            {
                $this->error = 5;
                return false;
            }
            $search = Array('@CITY@', '@STATE@', '@VIDEO_1@', '@DATE_START@', '@DATE_END@');
            $replace = Array($subdomain[0]['dealercity'], $subdomain[0]['dealerstate'], $BN_videoFile_1_1, $_REQUEST['datestart'], $_REQUEST['dateend']);
            $file = str_replace($search, $replace, $file);

            if($this->debug ) echo "<pre>$file</pre>";

            $tempFileName = $_SERVER['DOCUMENT_ROOT'].'/temp/'.md5(date("Y-M-d"));
            if(!$tempFile = fopen($tempFileName, "w+"))
            {
                $this->error = 6;
                return false;
            }
            if(!fwrite($tempFile, $file, 64000))
            {
                $this->error = 7;
                return false;
            }

            if(!ftp_put($ftpconn, '.'.$public_html.'/'.'index.htm', $tempFileName, FTP_ASCII))
            {
                $this->error = 4;
                return false;
            }
            fclose($tempFile);



            if(!$file = implode("", file($this->pathToTemplates[$_REQUEST['saletype']].'/landing/index.php')))
            {
                $this->error = 5;
                return false;
            }

            $search = Array('@CITY@', '@STATE@', '@DEALERNAME@', '@MIURL@', '@LOGIN@');
            if($landinginfo[0]['login'] == 'landingcreate') $replace = Array($subdomain[0]['dealercity'], $subdomain[0]['dealerstate'], $subdomain[0]['dealername'], $subdomain[0]['subdomain'].$this->postfix, $this->mi_login);
            else $replace = Array($subdomain[0]['dealercity'], $subdomain[0]['dealerstate'], $subdomain[0]['dealername'], $subdomain[0]['subdomain'].$this->postfix, $landinginfo[0]['login']);
            $file = str_replace($search, $replace, $file);

            if($this->debug ) echo "<pre>$file</pre>";

            $tempFileName = $_SERVER['DOCUMENT_ROOT'].'/temp/'.md5(date("Y-M-d"));
            if(!$tempFile = fopen($tempFileName, "w+"))
            {
                $this->error = 6;
                return false;
            }
            if(!fwrite($tempFile, $file, 64000))
            {
                $this->error = 7;
                return false;
            }

            if(!ftp_put($ftpconn, '.'.$public_html.'/landing/index.php', $tempFileName, FTP_ASCII))
            {
                $this->error = 4;
                return false;
            }
            fclose($tempFile);

            require_once($_SERVER['DOCUMENT_ROOT'].'/'.$subdomain[0]['subdomain'].'/config.php');
            if($db) mysql_close($db);
            if(!$db = mysql_connect($db_host, $db_username, $db_password))
            {
                $this->error = 10;
                return false;
            }
            if(!mysql_select_db($database, $db))
            {
                $this->error = 11;
                return false;
            }

            if($BN_videoFile_2_1) $video_2_str = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="340" height="226">
                            <param name="movie" value="'.$BN_videoFile_2_1.'">
                            <param name="quality" value="high">
                            <embed src="'.$BN_videoFile_2_1.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="340" height="226"></embed>
                            </object>';
            else $video_2_str = '';

            $register_template = implode("", file($_SERVER['DOCUMENT_ROOT'].'/sdtemplate/register_template.html'));
            $register_template = str_replace('@SUBDOMAIN@', $subdomain[0]['subdomain'], $register_template);
            $register_template = str_replace('<!--@VIDEO_2@-->', $video_2_str, $register_template);

            $query = "UPDATE geodesic_templates set template_code = '".stripslashes($register_template)."' WHERE template_id = 152";
            if($this->debug) echo "<hr>Query is ".$query."<hr>";

            if(!$result = mysql_query($query))
            {
                $this->error = 12;
                return false;
            }

            if(file_exists($videoFile_2_1))
            {
                if($this->debug) echo 'VideoFile_2_1: '.$videoFile_2_1.'<br>';
                ftp_put($ftp_mi_conn, './public_html/'.$subdomain[0]['subdomain'].'/'.$BN_videoFile_2_1, $videoFile_2_1, FTP_BINARY);
            }
            if(file_exists($videoFile_2_2))
            {
                if($this->debug) echo 'VideoFile_2_2: '.$videoFile_2_2.'<br>';
                ftp_put($ftp_mi_conn, './public_html/'.$subdomain[0]['subdomain'].'/'.$BN_videoFile_2_2, $videoFile_2_2, FTP_BINARY);
            }
            if(file_exists($clearOverAll))
            {
                if($this->debug) echo 'clearOverAll: '.$clearOverAll.'<br>';
                if(!ftp_put($ftp_mi_conn, './public_html/'.$subdomain[0]['subdomain'].'/'.$BN_clearOverAll, $clearOverAll, FTP_BINARY)) {$this->error = 8; return false;}
            }



            if(!$file = implode("", file($this->pathToTemplates[$_REQUEST['saletype']].'/Easy_Loan_App.htm')))
            {
                $this->error = 5;
                return false;
            }

            $search = Array('@CITY@', '@STATE@', '@DEALERNAME@', '@DATE_START@');
            $replace = Array($subdomain[0]['dealercity'], $subdomain[0]['dealerstate'], $subdomain[0]['dealername'], $_REQUEST['datestart']);
            $file = str_replace($search, $replace, $file);

            if($this->debug ) echo "<pre>$file</pre>";

            $tempFileName = $_SERVER['DOCUMENT_ROOT'].'/temp/'.md5(date("Y-M-d"));
            if(!$tempFile = fopen($tempFileName, "w+"))
            {
                $this->error = 6;
                return false;
            }
            if(!fwrite($tempFile, $file, 128000))
            {
                $this->error = 7;
                return false;
            }

            if(!ftp_put($ftpconn, '.'.$public_html.'/Easy_Loan_App.htm', $tempFileName, FTP_ASCII))
            {
                $this->error = 4;
                return false;
            }
            fclose($tempFile);

        }
        elseif($_REQUEST['saletype'] == 2)
        {
            if($BN_videoFile_1_1) $video_1_str = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="340" height="226">
                            <param name="movie" value="'.$BN_videoFile_1_1.'">
                            <param name="quality" value="high">
                            <embed src="'.$BN_videoFile_1_1.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="340" height="226"></embed>
                            </object>';
            else $video_1_str = '';

            if($BN_videoFile_2_1) $video_2_str = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="340" height="226">
                            <param name="movie" value="'.$BN_videoFile_2_1.'">
                            <param name="quality" value="high">
                            <embed src="'.$BN_videoFile_2_1.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="340" height="226"></embed>
                            </object>';
            else $video_2_str = '';


            if(!$file = implode("", file($this->pathToTemplates[$_REQUEST['saletype']].'/index.htm')))
            {
                $this->error = 5;
                return false;
            }

            $search = Array('@CITY@', '@DEALERNAME@', '@DATE_START@', '@VIDEO_1@');
            $replace = Array($subdomain[0]['dealercity'], $subdomain[0]['dealername'], $_REQUEST['datestart'], $video_1_str);
            $file = str_replace($search, $replace, $file);

            if($this->debug ) echo "<pre>$file</pre>";

            $tempFileName = $_SERVER['DOCUMENT_ROOT'].'/temp/'.md5(date("Y-M-d"));
            if(!$tempFile = fopen($tempFileName, "w+"))
            {
                $this->error = 6;
                return false;
            }
            if(!fwrite($tempFile, $file, 64000))
            {
                $this->error = 7;
                return false;
            }

            if(!ftp_put($ftpconn, '.'.$public_html.'/index.htm', $tempFileName, FTP_ASCII))
            {
                $this->error = 4;
                return false;
            }
            fclose($tempFile);



            if(!$file = implode("", file($this->pathToTemplates[$_REQUEST['saletype']].'/Thanks.htm')))
            {
                $this->error = 5;
                return false;
            }

            $search = Array('@VIDEO_2@');
            $replace = Array($video_2_str);
            $file = str_replace($search, $replace, $file);

               if($this->debug ) echo "<pre>$file</pre>";

            $tempFileName = $_SERVER['DOCUMENT_ROOT'].'/temp/'.md5(date("Y-M-d"));
            if(!$tempFile = fopen($tempFileName, "w+"))
            {
                $this->error = 6;
                return false;
            }
            if(!fwrite($tempFile, $file, 64000))
            {
                $this->error = 7;
                return false;
            }

            if(!ftp_put($ftpconn, '.'.$public_html.'/Thanks.htm', $tempFileName, FTP_ASCII))
            {
                $this->error = 4;
                return false;
            }
            fclose($tempFile);




            //Upload Google map
            if(!empty($_FILES['map']['tmp_name']) && !$error)
            {
                @ $map = $_FILES['map']['tmp_name'];
                  @ $map_name = $_FILES['map']['name'];
                  @ $map_size = $_FILES['map']['size'];
                  @ $map_type = $_FILES['map']['type'];
                  @ $map_error = $_FILES['map']['error'];

                if ($map_error > 0)
                {
                    switch($map_error)
                      {
                          case 1: $error = 19; break;
                          case 2: $errer = 20; break;
                          case 3: $error = 21; break;
                          case 4: $error = 22; break;
                    }
                }

                $upfile = $_SERVER['DOCUMENT_ROOT'].'/'.$map_name;

                if(is_uploaded_file($map))
                {
                    if(!move_uploaded_file($map, $upfile))
                      {
                        $error = 24;
                      }
                }
                else
                {
                    $errpr = 23;
                }
            }
            //Upload Google map

            if(!$file = implode("", file($this->pathToTemplates[$_REQUEST['saletype']].'/3Thanks.htm')))
            {
                $this->error = 5;
                return false;
            }

            $googleUrl = "http://maps.google.com/maps?f=q&hl=en&q=".$this->sites[0]['dealeraddress'].' '.$this->sites[0]['dealercity'].' '.$this->sites[0]['dealerstate'].' '.$this->sites[0]['dealerpostcode'];
            $googleUrl = str_replace(' ', '+', $googleUrl);

            $search = Array('@CITY@', '@DEALERNAME@', '@DATE_START@', '@GOOGLE_MAP@');
            $replace = Array($subdomain[0]['dealercity'], $subdomain[0]['dealername'], $_REQUEST['datestart'], "<a href=".$googleUrl." target=_blank><img src=googlemap.gif border=0 alt='Google maps'></a>");
            $file = str_replace($search, $replace, $file);

            if($this->debug ) echo "<pre>$file</pre>";

            $tempFileName = $_SERVER['DOCUMENT_ROOT'].'/temp/'.md5(date("Y-M-d"));
            if(!$tempFile = fopen($tempFileName, "w+"))
            {
                $this->error = 6;
                return false;
            }
            if(!fwrite($tempFile, $file, 64000))
            {
                $this->error = 7;
                return false;
            }

            if(!ftp_put($ftpconn, '.'.$public_html.'/3Thanks.htm', $tempFileName, FTP_ASCII))
            {
                $this->error = 4;
                return false;
            }

            if(!ftp_put($ftpconn, '.'.$public_html.'/googlemap.gif', $upfile, FTP_BINARY))
            {
                $this->error = 4;
                return false;
            }

            fclose($tempFile);

            /*
            if(!ftp_exec($ftpconn, "tar -xf cb_common.tar"))
            {
                $this->error = 8;
                return false;
            }
            */
        }



        //COPY_FILES_FROM_MOVINGIRON_TEMPLATES_TO_LANDING_SITE

        return("Sales site hase been added successfully.");
    }

    function show_form_download_leads()
    {
        $this->selectSites($b);
        $this->body = "        <style type='text/css'>
            /* Date */
            h2 {font-family: helvetica,arial,verdana,tahoma; font-size:22px; color:#0077cc;}
            td.first {background-color:#fafafa; align:right; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.second {background-color:#fffaf5; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.middle {background-color:#ecf4fc; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            td.middle1 {background-color:#ffff99; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            .small_text {font-family: helvetica,arial,verdana,tahoma; font-size:10px; color:#666666}

            /* Date */
            td.searchLeft        {padding:5px;}
            td.searchItem        {padding:5px 0}
            td.searchItemInd    {padding:5px 5px 5px 0}
            table.show  {border:0px; bgcolor:#cccccc; background-color:#cccccc; align:center; font-family: arial, helvetica, verdana; font-size:12px; width:80%;}
               .dateBtn    {background-image:url(./images/dayselect.gif); width:34px; background-repeat:no-repeat; background-position:middle center;}
               .calFont {font-family: helvetica,arial,verdana,tahoma; font-size:12px;}
            .text_red {color: #ff0000; text-weight: bold;}
           </style>
        <script language='JavaScript' src='calendar.js'></script>
        <script language='JavaScript'>
            function RefreshDates ()
            {
                var d = new Date();
                d.setTime(document.fls.fromMs.value);
                document.fls.DateFromValueDay.value = d.getDate();
                document.fls.DateFromValueMonth.value = d.getMonth()+1;
                document.fls.DateFromValueYear.value = d.getFullYear();
                d.setTime(document.fls.toMs.value);
                document.fls.DateToValueDay.value = d.getDate();
                document.fls.DateToValueMonth.value = d.getMonth()+1;
                document.fls.DateToValueYear.value = d.getFullYear();
                return (true);
            }
            function SetInitialDate ()
            {
                currField = document.fls.from;
                currHiddenField = document.fls.fromMs;
                setDate(d, m, y);
                wCoord = (screen.availWidth/2)-120;
                hCoord = (screen.availWidth/2)-250;
            }
            function url_change(site, miurl)
            {
                var sitename = document.fls.site.value;
                var reg=/http:\/\/www\.(\w+)\.(\w+)/;
                var reg1 = /http:\/\/(\w+)\.(\w+)/;
                if(reg.test(sitename) == true)
                {
                    var arr=reg.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else if(reg1.test(sitename) == true)
                {
                    var arr=reg1.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else
                {
                    alert(\"Incorrect site name!\");
                }
            }

    </script>";


        $this->body .= "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->body .= "<form  name='fls' id='fls' action=?a=105&act=process enctype='multipart/form-data' method=post>";

        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=left>Subdomain<td bgcolor=#fafafa width=50% align=left colspan=2 ><select name=subdomain>";

        for($i = 0; $i < $this->i; $i ++)
        {
            if($this->debug) echo "<hr>Sites: ".count($this->sites)."<hr>";

            if($this->sites[$i]['id'] == $_REQUEST['subdomain']) $this->body .= "<option value=".$this->sites[$i]['id']." selected>".$this->prefix.$this->sites[$i]['subdomain']."</option>";
            else $this->body .= "<option value=".$this->sites[$i]['id'].">".$this->sites[$i]['subdomain'].$this->postfix."</option>";
        }

        $this->body .= "</select></td></td></tr>";
        $this->body .= "<tr>
                        <td class=first width=50%>Date:</td>
                        <td class=second width=5%><input type='text' size='15' name='start' value='".$_REQUEST['start']."' class='dateInput'/></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal1', 'startAnc', 'start');\" class='dateBtn'></td>
                        </tr>
                        <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='startAnc'><div id='cal1' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle><a name='toAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        </tr>
                        ";

        $this->body .= "<tr><td bgcolor=#fafafa width=100% align=center colspan=3><input type=submit value='>>> Download >>>'></td></tr>";
        $this->body .= "</form>";
        $this->body .= "</table>";

        return $this->body;
    }

    function process_download_leads($b, $c)
    {
        $this->selectSites($b);
        $num = 0;

        $monthes = Array('January'=>1, 'February'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);

        $mon = explode(" ", $c);
        $c = $monthes[$mon[0]].", ".$mon[1].", ".$mon[2];

        $dateFrom = mktime(0, 0, 0, $monthes[$mon[0]], $mon[1], $mon[2]);
        $dateTo = mktime(23, 59, 59, $monthes[$mon[0]], $mon[1], $mon[2]);

        if($this->debug)
        {
            echo "c: $c<br>";
               echo "b: $b<br>";
            echo "from $dateFrom<br>";
            echo "to $dateTo<br>";
            echo "F: 0, 0, 0, $c<br>";
            echo "T: 23, 59, 59, $c<br>";
        }

        if($db) mysql_close($db);
        $path = $this->conf['webdirectory'].$this->sites[0]['subdomain']."/config.php";
        require_once($path);

        if(!$db = mysql_connect($api_db_host, $api_db_username, $api_db_password))
        {
            echo "Can't connect to database.";
            echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
        }
        if(!mysql_select_db($api_database, $db))
        {
            echo "Can't select database.";
            echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
        }

        $query = "SELECT
                 u.subdomain sale_site
                 , u.optional_field_10
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
                 , FROM_UNIXTIME(u.date_joined, \"%M %e, %Y\") signup_date
                 , u.referrer
                 FROM
                 geodesic_userdata u
                 LEFT JOIN geodesic_user_groups_price_plans pp
                 ON u.id = pp.id
                 WHERE 1 = 1
                    AND pp.group_id = 1
                    AND u.level = 0
                    AND u.subdomain = '".$this->sites[0]['subdomain'].$this->conf['postfix']."'
                    AND u.date_joined >= '".$dateFrom."' AND u.date_joined <= '".$dateTo."'

                    ORDER BY u.id DESC";

        $result = mysql_query($query);

        if($result)
        {
            if(mysql_num_rows($result) > 0)
            {
                $salt = date(Ymd);
                $filename = $this->conf['leadsFilename'].'-'.strtoupper($this->sites[0]['subdomain']).'-'.$salt.'.csv';
                $filePath = $this->conf['webdirectory'].$filename;

                if(!$file = fopen($filePath, "w"))
                {
                    echo "<br>Can't open file $filename<br>";
                    exit;
                }

                $headline = 'sale_site,';
                $headline .= 'new_used,';
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
                $num += mysql_num_rows($result);

                while($row = mysql_fetch_array($result))
                {
                    foreach($row as $rk => $rv)
                    {
                          $row[$rk] = preg_replace("#[\s]+#", ' ', $rv);
                    }

                    $string = trim(str_replace(",", "", $row['sale_site'])).",";
                    $string .= trim(str_replace(",", "", $row['optional_field_10'])).",";
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

                    fwrite($file, $string, 1024);
                }
                fclose($file);
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-Disposition: attachment; filename=$filename");

                readfile($filePath);
                //$pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
                unlink($filePath);
            }
            if(!$num)
            {
                $this->body = 'There are no registered users for '.$this->sites[0]['dealername'].'('.$this->sites[0]['subdomain'].$this->conf['postfix'].') (Date: '.$c.')';
                $this->printable = 1;
                return $this->body;
            }
            $this->printable = 0;
            return true;
        }
    }

    function show_form_download_leads_new()
    {
        if($_REQUEST['type'] == "site") {
            $site = $this->selectSites($_REQUEST['b']);
            $type_name = "Site";
            $site_n = $site[0]['subdomain'].$this->conf['postfix'];
        }
        elseif ($_REQUEST['type'] == 'sale') {
            $site = $this->selectLandings($_REQUEST['b']);
            $type_name = "Sale site";
            $site_n = $site[0]['domain'];
        }

        $this->body = "<style type='text/css'>
            /* Date */
            h2 {font-family: helvetica,arial,verdana,tahoma; font-size:22px; color:#0077cc;}
            td.first {background-color:#fafafa; align:right; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.second {background-color:#fffaf5; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.middle {background-color:#ecf4fc; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            td.middle1 {background-color:#ffff99; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            .small_text {font-family: helvetica,arial,verdana,tahoma; font-size:10px; color:#666666}

            /* Date */
            td.searchLeft        {padding:5px;}
            td.searchItem        {padding:5px 0}
            td.searchItemInd    {padding:5px 5px 5px 0}
            table.show  {border:0px; bgcolor:#cccccc; background-color:#cccccc; align:center; font-family: arial, helvetica, verdana; font-size:12px; width:80%;}
               .dateBtn    {background-image:url(./images/dayselect.gif); width:34px; background-repeat:no-repeat; background-position:middle center;}
               .calFont {font-family: helvetica,arial,verdana,tahoma; font-size:12px;}
            .text_red {color: #ff0000; text-weight: bold;}
           </style>
        <script language='JavaScript' src='calendar.js'></script>
        <script language='JavaScript'>
            function RefreshDates ()
            {
                var d = new Date();
                d.setTime(document.fls.fromMs.value);
                document.fls.DateFromValueDay.value = d.getDate();
                document.fls.DateFromValueMonth.value = d.getMonth()+1;
                document.fls.DateFromValueYear.value = d.getFullYear();
                d.setTime(document.fls.toMs.value);
                document.fls.DateToValueDay.value = d.getDate();
                document.fls.DateToValueMonth.value = d.getMonth()+1;
                document.fls.DateToValueYear.value = d.getFullYear();
                return (true);
            }
            function SetInitialDate ()
            {
                currField = document.fls.from;
                currHiddenField = document.fls.fromMs;
                setDate(d, m, y);
                wCoord = (screen.availWidth/2)-120;
                hCoord = (screen.availWidth/2)-250;
            }
            function url_change(site, miurl)
            {
                var sitename = document.fls.site.value;
                var reg=/http:\/\/www\.(\w+)\.(\w+)/;
                var reg1 = /http:\/\/(\w+)\.(\w+)/;
                if(reg.test(sitename) == true)
                {
                    var arr=reg.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else if(reg1.test(sitename) == true)
                {
                    var arr=reg1.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else
                {
                    alert(\"Incorrect site name!\");
                }
            }

    </script>";


        $this->body .= "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->body .= "<form  name='fls' id='fls' action='?a=106&b=".$_REQUEST['b']."&type=".$_REQUEST['type']."&act=process' enctype='multipart/form-data' method=post>";
        $this->body .='<tr><td colspan="3" bgcolor=#fafafa align="center"><b>Download leads</b></td></tr>';
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=center>".$type_name."</td><td bgcolor=#fafafa width=50% align=left colspan=2 >";
        $this->body .= $site_n;
        $this->body .= "</td></tr>";
        $this->body .= "<tr>
                        <td class=first width=50%>Start Date:</td>
                        <td class=second width=5%><input type='text' size='25' name='date_start' value='".$_REQUEST['date_start']."' class='dateInput'/></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal1', 'startAnc', 'date_start');\" class='dateBtn'></td>
                        </tr>
                        <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='startAnc'><div id='cal1' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle><a name='toAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        </tr>
                        ";
        $this->body .= "<tr>
                        <td class=first width=50%>End Date:</td>
                        <td class=second width=5%><input type='text' size='25' name='date_end' value='".$_REQUEST['date_end']."' class='dateInput'/></td>
                        <td class=second><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal1', 'startAnc', 'date_end');\" class='dateBtn'></td>
                        </tr>
                        <tr>
                        <td class=middle><spacer type='block' width='1' height='1' /></td>
                        <td class=middle><a name='startAnc'><div id='cal1' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        <td class=middle><a name='toAnc'><img src='i/d-t.gif' width='1' height='1' /></a></td>
                        </tr>
                        ";


        $this->body .= "<tr><td bgcolor=#fafafa width=100% align=center colspan=3><input type=submit value='>>> Download >>>'></td></tr>";
        $this->body .= "</form>";
        $this->body .= "</table>";

        return $this->body;
    }

    function process_download_leads_new($id , $type)
    {
        if($type == "site") {
            $site = $this->selectSites($id);
        }
        elseif($type == "sale") {
            $sale = $this->selectLandings($id);
        }

        $num = 0;

        $monthes = Array('January'=>1, 'February'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);

        $dateFrom = $dateTo = "";

        $mon = explode(" ", $_REQUEST['date_start']);
        if(count($mon) > 2) {
            $c = $monthes[$mon[0]].", ".$mon[1].", ".$mon[2];
            $dateFrom = mktime(0, 0, 0, $monthes[$mon[0]], $mon[1], $mon[2]);
        }

        $mon = explode(" ", $_REQUEST['date_end']);
        if(count($mon) > 2) {
           $dateTo = mktime(23, 59, 59, $monthes[$mon[0]], $mon[1], $mon[2]);
        }

        if($this->debug)
        {
            echo "c: $c<br>";
               echo "b: $b<br>";
            echo "from $dateFrom<br>";
            echo "to $dateTo<br>";
            echo "F: 0, 0, 0, $c<br>";
            echo "T: 23, 59, 59, $c<br>";
        }

        if($type == "site") {
            if($db) mysql_close($db);
            return $this->outSiteLeads($site, $dateFrom, $dateTo);
        }
        elseif($type=="sale") {
            return $this->outSaleLeads($sale, $dateFrom, $dateTo, $db);
        }
        $GLOBALS['printable'] = 1;
        return true;
    }

    function outSaleLeads($sale, $dateFrom, $dateTo) {
		// cbDebug( 'sale', $sale );	
		$subdomain  = $sale[0]['domain'];
		$subdomain  = str_replace( 'www.', '', $subdomain );
		$subdomain  = stripslashes(urldecode($subdomain));
		$subdomain  = parse_url( $subdomain );
		$subdomain  = ( isset( $subdomain['host'] ) )
					? $subdomain['host']
					: $subdomain['path'];
        $query = "SELECT
                 u.subdomain sale_site
                 , u.optional_field_10
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
                 , u.optional_field_1
                 FROM
                 leads_userdata u
                 WHERE 1 = 1
                 AND u.subdomain IN ('http://".$subdomain."/', 'http://www.".$subdomain."/') ";
        if(!empty($dateFrom))
		{
                $query .= " AND u.date_joined >= '".$dateFrom."'";
		}
        if(!empty($dateTo))
		{
                $query .= " AND u.date_joined <= '".$dateTo."' ";
        }
        $query .= " ORDER BY u.id DESC";

        if($this->debug) echo $query."<br/>";

		// cbDebug( 'query', $query );	
        $result = mysql_query($query);

        if($result)
        {
            if(mysql_num_rows($result) > 0)
            {
                $salt = date(Ymd);
                $filename = $this->conf['leadsFilename'].'-'.strtoupper($sale[0]['domain']).'-'.$salt.'.csv';
                $filePath = $this->conf['webdirectory'].$filename;

                if(!$file = fopen($filePath, "w"))
                {
                    echo "<br>Can't open file $filename<br>";
                    exit;
                }

                $headline = 'Website,';
                $headline = 'New or Used,';
                $headline .= 'First Name,';
                $headline .= 'Last Name,';
                $headline .= 'Phone,';
//                $headline .= 'phoneext,';
                $headline .= 'Mobile Phone,';
//                $headline .= 'phoneext_2,';
                $headline .= 'Email,';
                $headline .= 'Company,';
                $headline .= 'Street 1,';
                $headline .= 'Street 2,';
                $headline .= 'City,';
                $headline .= 'State,';
                $headline .= 'Postal Code,';
                $headline .= 'Country,';
                $headline .= 'Description,';
                $headline .= 'Member ID,';
                $headline .= 'Registered,';
                $headline .= 'Referrer' . "\n";

                fwrite($file, $headline, 1024);
                $num += mysql_num_rows($result);

                while($row = mysql_fetch_array($result))
                {
                    foreach($row as $rk => $rv)
                    {
                          $row[$rk] = preg_replace("#[\s]+#", ' ', $rv);
                    }

                    $string = trim(str_replace(",", "", $row['sale_site'])).",";
                    $string .= trim(str_replace(",", "", $row['optional_field_10'])).",";
                    $string .= trim(str_replace(",", "", $row['firstname'])).",";
                    $string .= trim(str_replace(",", "", $row['lastname'])).",";
                    $string .= trim(str_replace(",", "", $row['phone']));
                    $string .= ( $row['phoneext'] )
						? ' Ext. ' . trim(str_replace(",", "", $row['phoneext'])).","
						: ',';
                    $string .= trim(str_replace(",", "", $row['phone2']));
                    $string .= ( $row['phoneext_2'] )
						? ' Ext. ' . trim(str_replace(",", "", $row['phoneext_2'])).","
						: ',';
                    $string .= trim(str_replace(",", "", $row['email'])).",";
                    $string .= trim(str_replace(",", "", $row['company_name'])).",";
                    $string .= trim(str_replace(",", "", $row['address'])).",";
                    $string .= trim(str_replace(",", "", $row['address_1'])).",";
                    $string .= trim(str_replace(",", "", $row['city'])).",";
                    $string .= trim(str_replace(",", "", $row['state'])).",";
                    $string .= trim(str_replace(",", "", $row['zip'])).",";
                    $string .= trim(str_replace(",", "", $row['country'])).",";
					// description
                    $string .= 'Member ID: ' . trim(str_replace(",", "", $row['member_id']))." | ";
                    $string .= 'Registered: ' . trim(str_replace(",", "", $row['signup_date']))." | ";
                    $string .= 'Referrer URL: ' . trim(str_replace(",", "", $row['referrer'])).',';
                    $string .= trim(str_replace(",", "", $row['member_id'])).',';
                    $string .= trim(str_replace(",", "", $row['signup_date'])).',';
                    $string .= trim(str_replace(",", "", $row['referrer']))."\n";

                    fwrite($file, $string, 1024);
                }
                fclose($file);
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-Disposition: attachment; filename=$filename");

                readfile($filePath);
                //$pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
                unlink($filePath);
                exit();
            }
            if(!$num)
            {
                $this->body = '<b>There are no registered users for '.$sale[0]['dealername'].' ('.$sale[0]['domain'].')';
                if(!empty($dateFrom) && !empty($dateTo)) {
                   $this->body .= '<br>(Date range: '.$_REQUEST['date_start'].' - '.$_REQUEST['date_end'].')</b><br><br>';
                }
                $this->printable = 1;
                return $this->body;
            }
        }
    }

    function outSiteLeads($site, $dateFrom, $dateTo) {
        $conf = strtolower($site[0]['subdomain']);
        $ex = array('nobleford'=>'noble');
        if(array_key_exists(strtolower($conf), $ex)) {
            $conf = $ex[$conf];
        }

        // $path = $this->conf['webdirectory'].$conf."/config.php";
        $path = $this->conf['webdirectory']."/cp/config.php";

        if(is_file($path))
            require_once($path);
        else {
            $this->printable = 1;
            return "<b>Error : Configuration file for this subdomain not found.</b><br><br>";
        }


        if(false && !$db = mysql_connect($api_db_host, $api_db_username, $api_db_password))
        {
            echo "Can't connect to database.";
            echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
        }
        if(false && !mysql_select_db($api_database, $db))
        {
            echo "Can't select database.";
            echo "MySql errno: ".mysql_errno().". MySql error: ".mysql_error();
        }

        $query = "SELECT
                 u.subdomain sale_site
                 , u.optional_field_10
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
                 , FROM_UNIXTIME(u.date_joined, \"%M %e, %Y\") signup_date
                 , u.referrer
                 FROM
                 geodesic_userdata u
                 LEFT JOIN geodesic_user_groups_price_plans pp
                 ON u.id = pp.id
                 WHERE 1 = 1
                    AND pp.group_id = 1
                    AND u.level = 0
                    AND u.subdomain LIKE '%".$site[0]['subdomain'].$this->conf['postfix']."%'";

        if(!empty($dateFrom))
		{
                $query .= " AND u.date_joined >= '".$dateFrom."'";
		}
        if(!empty($dateTo))
		{
                $query .= " AND u.date_joined <= '".$dateTo."' ";
        }

                  $query .= " ORDER BY u.id DESC";

        $result = mysql_query($query);

        if($result)
        {
            if(mysql_num_rows($result) > 0)
            {
                $salt = date(Ymd);
                $filename = $this->conf['leadsFilename'].'-'.$conf.'-'.$salt.'.csv';
                $filePath = $this->conf['webdirectory'].$filename;

                if(!$file = fopen($filePath, "w"))
                {
                    echo "<br>Can't open file $filename<br>";
                    exit;
                }

                $headline = 'Website,';
                $headline = 'New or Used,';
                $headline .= 'First Name,';
                $headline .= 'Last Name,';
                $headline .= 'Phone,';
//                $headline .= 'phoneext,';
                $headline .= 'Mobile Phone,';
//                $headline .= 'phoneext_2,';
                $headline .= 'Email,';
                $headline .= 'Company,';
                $headline .= 'Street 1,';
                $headline .= 'Street 2,';
                $headline .= 'City,';
                $headline .= 'State,';
                $headline .= 'Postal Code,';
                $headline .= 'Country,';
                $headline .= 'Description,';
                $headline .= 'Member ID,';
                $headline .= 'Registered,';
                $headline .= 'Referrer' . "\n";

                fwrite($file, $headline, 1024);
                $num += mysql_num_rows($result);

                while($row = mysql_fetch_array($result))
                {
                    foreach($row as $rk => $rv)
                    {
                          $row[$rk] = preg_replace("#[\s]+#", ' ', $rv);
                    }

                    $string = trim(str_replace(",", "", $row['sale_site'])).",";
                    $string = trim(str_replace(",", "", $row['optional_field_10'])).",";
                    $string .= trim(str_replace(",", "", $row['firstname'])).",";
                    $string .= trim(str_replace(",", "", $row['lastname'])).",";
                    $string .= trim(str_replace(",", "", $row['phone']));
                    $string .= ( $row['phoneext'] )
						? ' Ext. ' . trim(str_replace(",", "", $row['phoneext'])).","
						: ',';
                    $string .= trim(str_replace(",", "", $row['phone2']));
                    $string .= ( $row['phoneext_2'] )
						? ' Ext. ' . trim(str_replace(",", "", $row['phoneext_2'])).","
						: ',';
                    $string .= trim(str_replace(",", "", $row['email'])).",";
                    $string .= trim(str_replace(",", "", $row['company_name'])).",";
                    $string .= trim(str_replace(",", "", $row['address'])).",";
                    $string .= trim(str_replace(",", "", $row['address_1'])).",";
                    $string .= trim(str_replace(",", "", $row['city'])).",";
                    $string .= trim(str_replace(",", "", $row['state'])).",";
                    $string .= trim(str_replace(",", "", $row['zip'])).",";
                    $string .= trim(str_replace(",", "", $row['country'])).",";
					// description
                    $string .= 'Member ID: ' . trim(str_replace(",", "", $row['member_id']))." | ";
                    $string .= 'Registered: ' . trim(str_replace(",", "", $row['signup_date']))." | ";
                    $string .= 'Referrer URL: ' . trim(str_replace(",", "", $row['referrer'])).',';
                    $string .= trim(str_replace(",", "", $row['member_id'])).',';
                    $string .= trim(str_replace(",", "", $row['signup_date'])).',';
                    $string .= trim(str_replace(",", "", $row['referrer']))."\n";

                    fwrite($file, $string, 1024);
                }
                fclose($file);
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-Disposition: attachment; filename=$filename");

                readfile($filePath);
                //$pathToFile = $_SERVER["DOCUMENT_ROOT"] . '/scripts/' . $filename;
                unlink($filePath);
                exit();
            }
            if(!$num)
            {
                $this->body = '<b>There are no registered users for ' .  $site[0]['dealername'].' ('.$site[0]['subdomain'].$this->conf['postfix'].') ';
                if(!empty($dateFrom) && !empty($dateTo)) {
                   $this->body .= '<br>(Date range: '.$_REQUEST['date_start'].' - '.$_REQUEST['date_end'].')</b><br><br>';
                }
                $this->printable = 1;
                return $this->body;
            }
        }

    }

    /*
    *Parameter $a - List type (1 - Leads list for Geodesic Auctions/Classifieds system, 2 - Coll list for sales)
    *Parameter $b - domain id
    */
    function show_form_maillist_create($b,$c)
    {
        if(!$b || !$c)
        {
            $this->error = 26;
            return false;
        }
        if($b == 1)
        {
            $this->selectSites($c);
            if(empty($this->sites[0]['id']))
            {
                $this->error = 27;
                return false;
            }
        }
        elseif($b == 2)
        {
            $this->selectLandings($c);
            if(empty($this->landings[0]['id']))
            {
                $this->error = 28;
                return false;
            }
        }

        $this->body = '';
        if($this->errors) $this->body .= $this->show_errors();

        if(!$this->conn->query("
			SELECT * 
			FROM maillist 
			WHERE 
				domain=".$c."
				AND type=".$b
			)
		)
        {
            echo $this->conn->error;
            return false;
        }
        if($row = $this->conn->fetchRow())
        {
            $id = $row['id'];
            $domain = $row['domain'];
            $active = $row['active'];
            $type = $row['type'];
            $datefrom = $row['datefrom'];
            $dateto = $row['dateto'];
            $subject = stripslashes(urldecode($row['subject']));
            $message = stripslashes(urldecode($row['message']));
            $contactsto = stripslashes(urldecode($row['contactsto']));
            $contactscc = stripslashes(urldecode($row['contactscc']));
            $contactsbcc = stripslashes(urldecode($row['contactsbcc']));
            $times = stripslashes(urldecode($row['times']));
            $interval = stripslashes(urldecode($row['interval']));
			$fakeId				= false;
        }
		else
		{
            $subdomain			= $this->sites[0]['subdomain'];
            $domain				= $subdomain . $this->conf['postfix'];
            $active				= 1;
            $type				= 2;
			$datefrom           = time();
			$dateto             = $datefrom + ( 11 * 24 * 60 * 60 );
            $subject			= $this->conf['siteName'] . ': Leads for ' .  $domain;
            $message			= $subject;
            // $contactsto			= $this->conf['email'];
            $contactsto			= $subdomain . $this->conf['emailBase'];
            $contactscc			= '';
            $contactsbcc		= '';
            $times				= 1;
            $interval			= $this->interval_def;
			$fakeId				= true;
		}

        if($times)
        {
			// MLC 20070810 no row, no data, but grab default
			$id					= ( ! $fakeId ) ? $id : $times;

            if(!$this->conn->query("
					SELECT * 
					FROM times 
					WHERE maillistid=".$id." 
						AND active = 1
					ORDER BY hour
				")
			)
            {
                echo $this->conn->error;
                return false;
            }

			// MLC 20070810 no row, no data, but grab default
			if ( ! $fakeId )
			{
				unset( $id );
			}

            $j = 0;
            while($row = $this->conn->fetchRow())
            {
                $time[$j]['maillistid'] = $row['maillistid'];
                $time[$j]['hour'] = $row['hour'];
                $time[$j]['minutes'] = $row['minutes'];
                $time[$j]['use_pdf'] = $row['use_pdf'];

                $hour[$j] = $row['hour'];
                $minutes[$j] = $row['minutes'];
                $use_pdf[$j] = $row['use_pdf'];

                $j ++;
            }
        }

        if($_REQUEST['$id']) $id = $_REQUEST['id'];
        if($_REQUEST['active']) $active = $_REQUEST['active'];
        if($_REQUEST['datefrom']) $datefrom = $_REQUEST['datefrom'];
        if($_REQUEST['dateto']) $dateto = $_REQUEST['dateto'];
        if($_REQUEST['subject']) $subject = trim($_REQUEST['subject']);
        if($_REQUEST['message']) $message = trim($_REQUEST['message']);
        if($_REQUEST['contactsto']) $contactsto = trim($_REQUEST['contactsto']);
        if($_REQUEST['contactscc']) $contactscc = trim($_REQUEST['contactscc']);
        if($_REQUEST['contactsbcc']) $contactsbcc = trim($_REQUEST['contactsbcc']);
        if($_REQUEST['hour']) $hour = $_REQUEST['hour'];
        if($_REQUEST['minutes']) $minutes = $_REQUEST['minutes'];
        if($_REQUEST['use_pdf']) $use_pdf = $_REQUEST['use_pdf'];
        if($_REQUEST['interval']) $interval = $_REQUEST['interval'];

        if($this->debug)
        {
            foreach($_REQUEST as $key => $value)
            {
                echo "<br>".$key." => ".$value;
                if(is_array($value)) foreach($value as $k => $v) echo "<hr>".$k." ===> ".$v;
            }
        }

        $monthes = Array('January'=>1, 'February'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);
        if($datefrom && !empty($_REQUEST['datefrom']))
        {
            $dfrom = explode(" ", $datefrom);
            $datefrom = mktime(0,0,0,$monthes[$dfrom[0]],$dfrom[1],$dfrom[2]);
        }
        if($dateto && !empty($_REQUEST['dateto']))
        {
            $dto = explode(" ", $dateto);
            $dateto = mktime(0,0,0,$monthes[$dto[0]],$dto[1],$dto[2]);
        }
        elseif($dateto && empty($_REQUEST['dateto'])) $dateto -= 3600*24;

        // if(!$datefrom && !$this->error) $datefrom = time();
        // if(!$dateto && !$this->error) $dateto = time() + 3600*24;
        if(!$interval && !$this->error) $interval = $this->interval->def;

        $this->body .= $this->styles();
        $this->body .= "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->body .= "<form name=addSaleInfo action=?a=110&b=".$b."&c=".$c."&act=process enctype='multipart/form-data' method=post>";
        $this->body .= "<input type=hidden name=id value=".$id.">";
        $this->body .= "<tr><th colspan=3><strong>Edit Mailing List Information</strong></th></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Active:</td>";
        if($active)
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><input type=checkbox name=active checked value=1></td></tr>";
        else
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><input type=checkbox name=active value=1></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Date From:<span class=text_bold_red> *</span></td>";
        if($datefrom) $this->body .= "<td bgcolor=#fafafa width=33% align=left><input type=text name=datefrom value='".date("F d Y", $datefrom)."'></td>";
        else $this->body .= "<td bgcolor=#fafafa width=33% align=left><input type=text name=datefrom value=''></td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal1', 'datefromAnc', 'datefrom');\" class='dateBtn'></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left></td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left><a name='datefromAnc'><div id='cal1' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div></a></td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Date To:<span class=text_bold_red> *</span></td>";
        if($dateto) $this->body .= "<td bgcolor=#fafafa width=33% align=left><input type=text name=dateto value='".date("F d Y", $dateto)."'></td>";
        else  $this->body .= "<td bgcolor=#fafafa width=33% align=left><input type=text name=dateto value=''></td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left><input type='button' value='&nbsp;' width='' onMouseDown=\"showCalendar(this, 'cal2', 'datetoAnc', 'dateto');\" class='dateBtn'></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left></td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left><a name='datetoAnc'><div id='cal2' style='position: absolute; top: 200; left: 10; z-index: 666; visibility: hidden;'></div></a></td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Subject:<span class=text_bold_red> *</span></td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><textarea cols=50 rows=3 name=subject>".$subject."</textarea></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Message:<span class=text_bold_red> *</span></td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><textarea cols=50 rows=3 name=message>".$message."</textarea></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Contacts To:<span class=text_bold_red> *</span></td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><textarea cols=50 rows=3 name=contactsto>".$contactsto."</textarea></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left></td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><input type=submit value='>> Save >>'></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Contacts Cc:</td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><textarea cols=50 rows=3 name=contactscc>".$contactscc."</textarea></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Contacts Bcc:</td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><textarea cols=50 rows=3 name=contactsbcc>".$contactsbcc."</textarea></td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Interval:<span class=text_bold_red> *</span></td>";
        $this->body .= "<td bgcolor=#fafafa width=66% align=left><input name=interval value=".$interval."></td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left>";
		$this->body .= "In hours; 24 is past day, 48 is past two days</td></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Current Server Time:</td>";
        $this->body .= "<td bgcolor=#fafafa width=66% align=left>"
					. date( 'r T' )
					. "</td>";
        $this->body .= "<td bgcolor=#fafafa width=33% align=left>&nbsp;</td></tr>";

        $min = Array('0' => '00', '15' => '15', '30' => '30', '45' => '45', '59' => '59');
        for($j = 0; $j < 24; $j ++)
        {
            $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>List ".($j + 1).":</td>";
            $timestr = "<select name=hour[".$j."]>";
            $timestr .= "<option value=NONE default>No Select</option>";
            for($h = 0; $h < 24; $h ++)
            {
                if($this->debug) echo "$h == ".$hour[$j]." && !empty(".$hour[$j].")<hr color=#ff0000>";
                if(is_numeric($hour[$j]) && ($h == intval($hour[$j]))) $timestr .= "<option value=".$h." selected>".$h."</option>";
                else $timestr .= "<option value=".$h.">".$h."</option>";
            }
            $timestr .= "</select> ";

            $timestr .= "<select name=minutes[".$j."]>";
            $timestr .= "<option value=NONE default>No Select</option>";
            foreach($min as $key => $val)
            {
                if($this->debug) echo "<hr color=#00ff00>$key == ".$minutes[$j]." && !empty(".$minutes[$j].")";
                if(is_numeric($minutes[$j]) && (intval($key) == intval($minutes[$j]))) $timestr .= "<option value=".$key." selected>".$val."</option>";
                else $timestr .= "<option value=".$key.">".$val."</option>";
            }
            $timestr .= "</select>";

            $this->body .= "<td bgcolor=#fafafa width=33% align=left>".$timestr."</td>";
            $this->body .= "<td bgcolor=#fafafa width=33% align=left>";
            if($use_pdf[$j]) $this->body .= "<input name=use_pdf[".$j."] value=1 type=checkbox checked> Send Mail List in PDF format</td></tr>";
            else $this->body .= "<input name=use_pdf[".$j."] value=1 type=checkbox> Send Mail List in PDF format</td></tr>";
        }

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left></td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left><input type=submit value='>> Save >>'></td></tr>";

        $this->body .= "</form>";
        $this->body .= "</table>";

        return $this->body;
    }

    function process_create_maillist($b, $c)
    {
        if(!$b || !$c)
        {
            $this->error = 26;
            return false;
        }
        if($b == 1)
        {
            $this->selectSites($c);
            if(empty($this->sites[0]['id']))
            {
                $this->error = 27;
                return false;
            }
        }
        elseif($b == 2)
        {
            $this->selectLandings($c);
            if(empty($this->landings[0]['id']))
            {
                $this->error = 28;
                return false;
            }
        }

        require_once($this->DOCUMENT_ROOT."/cp/classes/verifier_class.php");

        $id = $_REQUEST['id'];
        $active = $_REQUEST['active'];
        $datefrom = trim($_REQUEST['datefrom']);
        $dateto = trim($_REQUEST['dateto']);
        $subject = trim($_REQUEST['subject']);
        $message = trim($_REQUEST['message']);
        $contactsto = trim($_REQUEST['contactsto']);
        $contactscc = trim($_REQUEST['contactscc']);
        $contactsbcc = trim($_REQUEST['contactsbcc']);
        $hour = $_REQUEST['hour'];
        $minutes = $_REQUEST['minutes'];
        $use_pdf = $_REQUEST['use_pdf'];
        $interval = trim($_REQUEST['interval']);

        $timenum = 0;
        for($j = 0; $j < 24; $j ++)
        {
            if($this->debug) echo $timenum." -> Pre Timenum<br>";
            if(is_numeric($hour[$j]) && is_numeric($minutes[$j]))
            {
                $timenum ++;
                if($this->debug) echo $timenum." -> Timenum<br>";
            }
        }

        if($active)
        {
            $verifier = new verifier();
            // Verification of date fields
            if(!$verifier->verificate_date_data($datefrom, 'F d Y')) {$this->error = 1; $this->errors['datefrom'] = 1;}
            if(!$verifier->verificate_date_data($dateto, 'F d Y')) {$this->error = 1; $this->errors['dateto'] = 1;}
            // Verification of date fields

            //Verificateon of interval field
            if(!$verifier->verificate_int_data($interval)) {$this->error = 1; $this->errors['interval'] = 1;}
            //Verificateon of interval field

            //Verification of mail fields
            if(!$verifier->verificate_mail_address_data($contactsto)) {$valid = 0; $this->errors['contactsto'] = 1;}
            if(!empty($contactscc)) if(!$verifier->verificate_mail_address_data($contactscc)) {$this->error = 1; $this->errors['contactscc'] = 1;}
            if(!empty($contactsbcc)) if(!$verifier->verificate_mail_address_data($contactsbcc)) {$this->error = 1; $this->errors['contactsbcc'] = 1;}
            //Verification of mail fields

            if(empty($datefrom) || empty($dateto) || empty($subject) || empty($message) || empty($contactsto) || empty($timenum) || $this->error)
            {
                if(empty($datefrom)) {$this->error = 1; $this->errors['datefrom'] = 1;}
                if(empty($dateto)) {$this->error = 1; $this->errors['dateto'] = 1;}
                if(empty($subject)) {$this->error = 1; $this->errors['subject'] = 1;}
                if(empty($message)) {$this->error = 1; $this->errors['message'] = 1;}
                if(empty($contactsto)) {$this->error = 1; $this->errors['contactsto'] = 1;}
                if(empty($timenum)) {$this->error = 1; $this->errors['timenum'] = 1;}

                foreach($_REQUEST as $key => $value)
                {
                    $request .= ($request) ? $value:"&".$value;
                }
                //header("Location: ".$_SERVER['HTTP_HOST']."/cp/?".$request);
                return $this->show_form_maillist_create($b,$c);
            }
        }

        $monthes = Array('January'=>1, 'February'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);
        if($datefrom)
        {
            $dfrom = explode(" ", $datefrom);
            $datefrom = mktime(0,0,0,$monthes[$dfrom[0]],$dfrom[1],$dfrom[2]);
        }
        if($dateto)
        {
            $dto = explode(" ", $dateto);
            $dateto = mktime(0,0,0,$monthes[$dto[0]],$dto[1]+1,$dto[2]);
        }

        if(!$active) $active = 0;
        if(!$interval) $interval = $this->interval_def;
        if(!$datefrom) $datefrom = time();
        if(!$dateto) $dateto = time() + 3600*24;

        if(!$this->conn->query("SELECT `id` FROM `maillist` WHERE `domain`=".$c." AND `type`=".$b))
        {
            $this->error = 30;
            return false;
        }
        $row = $this->conn->fetchRow();
        $id = $row['id'];

        if(!$id)
        {
            $query = "INSERT INTO `maillist` (`id`, `domain`, `active`, `type`, `datefrom`, `dateto`, `subject`, `message`, `contactsto`, `contactscc`, `contactsbcc`, `times`, `interval`)".
            " VALUES(".
            "'', ".
            "\"".urlencode(addslashes(trim($c)))."\", ".
            "\"".urlencode(addslashes(trim($active)))."\", ".
            "\"".urlencode(addslashes(trim($b)))."\", ".
            "\"".urlencode(addslashes(trim($datefrom)))."\", ".
            "\"".urlencode(addslashes(trim($dateto)))."\", ".
            "\"".urlencode(addslashes(trim($subject)))."\", ".
            "\"".urlencode(addslashes(trim($message)))."\", ".
            "\"".urlencode(addslashes(trim($contactsto)))."\", ".
            "\"".urlencode(addslashes(trim($contactscc)))."\", ".
            "\"".urlencode(addslashes(trim($contactsbcc)))."\", ".
            "\"".urlencode(addslashes(trim($timenum)))."\", ".
            "\"".urlencode(addslashes(trim($interval)))."\"".
            ")";

            if(!$this->conn->query($query))
            {
                $this->error = "29"." ".$query;

                return false;
            }

            if(!$this->conn->query("SELECT `id` FROM `maillist` WHERE `domain`=".$c." AND `type`=".$b))
            {
                $this->error = 30;
                return false;
            }
            $row = $this->conn->fetchRow();
            $id = $row['id'];

            if(!$this->conn->query("DELETE FROM `times` WHERE `maillistid`=".$id))
            {
                $this->error = 31;
                return false;
            }

            $query = '';
            for($j = 0; $j < 24; $j ++)
            {
				if ( $hour[ $j ] && 'NONE' == $minutes[ $j ] )
				{
					$minutes[ $j ] 	= 0;
				}

                if(is_numeric($hour[$j]) && is_numeric($minutes[$j]))
                {
                    if(!$use_pdf[$j]) $use_pdf[$j] = 0;
                    $query .= ($query)
						? ",(".$id.", ".$hour[$j].", ".$minutes[$j].", ".$use_pdf[$j].", 1)"
						:"(".$id.", ".$hour[$j].", ".$minutes[$j].", ".$use_pdf[$j].", 1)";
                }
            }
            $query = "REPLACE INTO `times` (`maillistid`, `hour`, `minutes`, `use_pdf`, `active`) VALUES ".$query;

            if($timenum)
            {
                if(!$this->conn->query($query))
                {
                    $this->error = "32"." ".$query; //Can't execute query: $query
                    return false;
                }
            }
        }
        else
        {
            $query = "UPDATE `maillist` SET ".
            "`domain` = ".$c.", ".
            "`active` = ".$active.", ".
            "`type` = ".$b.", ".
            "`datefrom` = '".$datefrom."', ".
            "`dateto` = '".$dateto."', ".
            "`subject` = '".addslashes(urlencode(trim($subject)))."', ".
            "`message` = '".addslashes(urlencode(trim($message)))."', ".
            "`contactsto` = '".addslashes(urlencode(trim($contactsto)))."', ".
            "`contactscc` = '".addslashes(urlencode(trim($contactscc)))."', ".
            "`contactsbcc` = '".addslashes(urlencode(trim($contactsbcc)))."', ".
            "`times` = ".$timenum.", ".
            "`interval` = ".$interval." ".
            "WHERE `id`=".$id;

            if(!$this->conn->query($query))
            {
                $this->error = "29"." ".$query;
                return false;
            }

            if(!$this->conn->query("DELETE FROM `times` WHERE `maillistid`=".$id))
            {
                $this->error = 31;
                return false;
            }

            $query = '';
            for($j = 0; $j < 24; $j ++)
            {
				if ( $hour[ $j ] && 'NONE' == $minutes[ $j ] )
				{
					$minutes[ $j ] 	= 0;
				}

                if(is_numeric($hour[$j]) && is_numeric($minutes[$j]))
                {
                    if(!$use_pdf[$j]) $use_pdf[$j] = 0;
                    $query .= ($query) 
						? ",(".$id.", ".$hour[$j].", ".$minutes[$j].", ".$use_pdf[$j].", 1)"
						:"(".$id.", ".$hour[$j].", ".$minutes[$j].", ".$use_pdf[$j].", 1)";
                }
            }
            $query = "REPLACE INTO `times` (`maillistid`, `hour`, `minutes`, `use_pdf`, `active`) VALUES ".$query;

            if($timenum)
            {
                if(!$this->conn->query($query))
                {
                    $this->error = "32"." ".$query; //Can't execute query: $query
                    return false;
                }
            }
        }
        return "Mailing List Data has been updated successfully.";
    }


    function styles()
    {
        $str = "<style type='text/css'>
            /* Date */
            h2 {font-family: helvetica,arial,verdana,tahoma; font-size:22px; color:#0077cc;}
            td.first {background-color:#fafafa; align:right; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.second {background-color:#fffaf5; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 10px;}
            td.middle {background-color:#ecf4fc; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            td.middle1 {background-color:#ffff99; align:left; font-family: arial, helvetica, verdana; font-size:12px; padding: 2px;}
            .small_text {font-family: helvetica,arial,verdana,tahoma; font-size:10px; color:#666666}

            /* Date */
            td.searchLeft        {padding:5px;}
            td.searchItem        {padding:5px 0}
            td.searchItemInd    {padding:5px 5px 5px 0}
            table.show  {border:0px; bgcolor:#cccccc; background-color:#cccccc; align:center; font-family: arial, helvetica, verdana; font-size:12px; width:80%;}
                   .dateBtn    {background-image:url(./images/dayselect.gif); width:34px; background-repeat:no-repeat; background-position:middle center;}
                   .calFont {font-family: helvetica,arial,verdana,tahoma; font-size:12px;}
                .text_red {color: #ff0000; text-weight: bold;}
                   </style>
                <script language='JavaScript' src='calendar.js'></script>
                <script language='JavaScript'>
            function RefreshDates ()
            {
                var d = new Date();
                d.setTime(document.fls.fromMs.value);
                document.fls.DateFromValueDay.value = d.getDate();
                document.fls.DateFromValueMonth.value = d.getMonth()+1;
                document.fls.DateFromValueYear.value = d.getFullYear();
                d.setTime(document.fls.toMs.value);
                document.fls.DateToValueDay.value = d.getDate();
                document.fls.DateToValueMonth.value = d.getMonth()+1;
                document.fls.DateToValueYear.value = d.getFullYear();
                return (true);
            }
            function SetInitialDate ()
            {
                currField = document.fls.from;
                currHiddenField = document.fls.fromMs;
                setDate(d, m, y);
                wCoord = (screen.availWidth/2)-120;
                hCoord = (screen.availWidth/2)-250;
            }
            function url_change(site, miurl)
            {
                var sitename = document.fls.site.value;
                var reg=/http:\/\/www\.(\w+)\.(\w+)/;
                var reg1 = /http:\/\/(\w+)\.(\w+)/;
                if(reg.test(sitename) == true)
                {
                    var arr=reg.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else if(reg1.test(sitename) == true)
                {
                    var arr=reg1.exec(sitename);
                    document.fls.miurl.value = \"http://\" + arr[1] + \"\.movingiron.com\";
                }
                else
                {
                    alert(\"Incorrect site name!\");
                }
            }

    </script>";

    return $str;
    }

    function show_errors()
    {
        if($this->error)
        {
            $this->body = "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#FF0000>";
            $this->body .= "<tr><td bgcolor=#FFFF66 align=left valign=top><strong>Errors!</strong><br>";

            foreach($this->errors as $errkey => $errval)
            {
                if($errval)
                {
                    $this->body .= $this->error_messages[$errkey]."<br>";
                }
            }

            $this->body .= "</td></tr></table><br>";
        }
    }

	function show_form_delete_landing($b)
	{
        $this->body .= $this->styles();
        $this->body .= "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->body .= "<form name=deleteLandingInfo action=?a=103&b=".$b." enctype='multipart/form-data' method=post>";
        $this->body .= "<tr><th colspan=3><strong>Delete Landing Site Information</strong></th></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Delete Landing Site?</td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left>Yes <input type=radio name=act value=process>";
        $this->body .= "No <input type=radio name=act value=false></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=center
		colspan=2><input type=submit value='>>> Delete Landing Site >>>'></td></tr>";
        $this->body .= "</form>";
        $this->body .= "</table>";

        return $this->body;
	}

	function process_delete_landing_info($b)
	{
		if(!$this->conn->query("UPDATE landingsites SET deleted = 1 WHERE id = $b"))
		{
			$this->error = 1000;
			return false;
		}
		else
		{
			$this->body			.= "Site deleted";
			return $this->body;
		}
	}

	function show_form_delete_site($b,$c)
	{
        $this->body .= $this->styles();
        $this->body .= "<table width=95% cellpadding=5 cellspacing=1 bgcolor=#cccccc>";
        $this->body .= "<form name=deleteLandingInfo action=?a=101&b=".$b."&c=".$c." enctype='multipart/form-data' method=post>";
        $this->body .= "<tr><th colspan=3><strong>Delete Inventory Site Information</strong></th></tr>";

        $this->body .= "<tr><td bgcolor=#fafafa width=33% align=left>Delete Inventory Site?</td>";
        $this->body .= "<td colspan=2 bgcolor=#fafafa width=66% align=left>Yes <input type=radio name=act value=process>";
        $this->body .= "No <input type=radio name=act value=false></td></tr>";
        $this->body .= "<tr><td bgcolor=#fafafa width=50% align=center
		colspan=2><input type=submit value='>>> Delete Inventory Site >>>'></td></tr>";
        $this->body .= "</form>";
        $this->body .= "</table>";

        return $this->body;
	}

	function process_delete_site_info($b, $subdomain)
	{
		require_once('create_site_class.php');
		global $cpConfig, $db_prefix;

		require_once('../'.$subdomain.'/config.php');

		$site = new siteCreate($subdomain, $db_username, $db_password, $database, $db_prefix, $cpConfig);

		$api_db = mysql_connect($api_db_host, $api_db_username, $api_db_password);
		if ( ! $api_db || ! mysql_select_db($api_database, $api_db) ) {
			$this->body			.= "Database API not connected";
			return $this->body;
		}

		$site->del_api($api_db);
		$site->del_api_users($api_db);
		$site->del_db();
		$site->del_user();
		$site->del_subdomain();
		$site->del_subdomain_folder();

		if ( count( $site->answers ) ) {
			$this->body			.= "Responses<br />";
			$this->body			.= implode('<br />', $site->answers);
		}

		if ( count( $site->errors ) ) {
			$this->body			.= "<br />Errors<br />";
			$this->body			.= implode('<br />', $site->errors);
		}

		if(!$this->conn->query("UPDATE sites SET deleted = 1 WHERE id = $b"))
		{
			$this->error = 1001;
			return false;
		}
		else
		{
			$this->body			.= "<h2>Inventory site deleted</h2>";
			return $this->body;
		}
	}
}

?>
