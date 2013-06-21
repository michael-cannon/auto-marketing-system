<?php
set_time_limit(36000);

class siteCreate
{
	var $error = 0;
    var $baseSite = '';
	var $logpass = '';
	var $answer = '';
	var $subdomain = '';
	var $userName = '';
	var $userPassword = '';
	var $dbName = '';
	var $prefix	= '';
	var $cpanelUrl = '';
	var $answers = Array();
	var $errors = Array();
	var $conf = Array();

	function siteCreate($subdomain, $userName, $userPassword, $dbName, $prefix, $conf)
    {
    	$this->conf = $conf;
    	$this->subdomain = $subdomain;
        $this->userName = $userName;
        $this->userPassword = $userPassword;
        $this->dbName = $dbName;
        $this->prefix = $prefix;
    	$this->baseSite = $this->conf['baseSite'];
		$this->logpass = $this->conf['logpass'];
		$this->cpanelUrl = $this->conf['cpanelUrl'];
    }

	//User name and password for http authentication
    /*
    * @param 
    */
	function create_subdomain()
    {
   		$ch = curl_init();
   		curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
   		curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/subdomain/doadddomain.html");
   		curl_setopt($ch, CURLOPT_POST, 1 );
   		curl_setopt($ch, CURLOPT_POSTFIELDS, 'domain='.$this->subdomain.'&rootdomain='.$this->conf['mi_host'].'&dir=public_html%2F'.$this->subdomain);
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   		$postResult = curl_exec($ch);

   		if(curl_errno($ch))
    	{
            curl_close($ch);
            $this->errors['subdomain'] = 'unable to create subdomain';
            return(0);
   		}
   		curl_close($ch);
		if(stristr($postResult, 'has been created') === FALSE) return(0);
        $this->answers['subdomain'] = 'subdomain created';
		return(1);
    }

    /**
	 * Create email fowards
     * @param 
     */
	function create_email_forward($inEmail, $emailBase, $forwardToEmail)
    {
		$forwardToEmail = urlencode( $forwardToEmail );
   		$ch = curl_init();
   		curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
   		curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/mail/doaddfwd.html");
   		curl_setopt($ch, CURLOPT_POST, 1 );
   		curl_setopt($ch, CURLOPT_POSTFIELDS, "email=$inEmail&domain=$emailBase&fwdopt=fwd&fwdemail=$forwardToEmail&failmsgs=No+such+person+at+this+address&pipefwd=");
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   		$postResult = curl_exec($ch);

   		if(curl_errno($ch))
    	{
            curl_close($ch);
            $this->errors['email_foward'] = $inEmail . ' unable to create email forward';
            return(0);
   		}
   		curl_close($ch);
		if(stristr($postResult, 'will now be copied to') === FALSE) return(0);
        $this->answers['email_foward'] = $inEmail . ' email forward created';
		return(1);
    }

    /*
    * @param
    */
    function create_db()
    {
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/sql/addb.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'db='.$this->dbName.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['db'] = "<bCan't create database".$this->dbName.".</b>";
            return(0);
        }
        curl_close($ch);
        if(stristr($postResult, 'Added the database') === FALSE) return(0);
        $this->answers['db'] = "<b>Database Created:</b> Added the database ".$this->dbName.".";
        return(1);
    }

    /*
    * @param
    */
    function create_user()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/sql/adduser.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'user='.$this->userName.'&pass='.$this->userPassword.'&pass2='.$this->userPassword);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['user'] = "<b>Can't create user: ".$this->userName.":".$this->userPassword."</b>";
            return(0);
        }
        curl_close($ch);
        if(stristr($postResult, 'Added user') === FALSE) return(0);
        $this->answers['user'] = "<b>Account Created:</b> Added ".$this->userName." with the password: ".$this->userPassword.".";
        return(1);
    }

    /*
    * @param
    */
    function add_user_to_db()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/sql/addusertodb.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'user='.$this->userName.'&db='.$this->dbName.'&ALL=ALL');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['usertodb'] = "<b>Can't add user".$this->userName." to the ".$this->dbName.".</b>";
            return(0);
        }
        curl_close($ch);
        if(stristr($postResult, 'was added to the database') === FALSE) return(0);
        $this->answers['usertodb'] = "<b>Account added to Access List.</b> Added the user ".$this->userName." to the ".$this->dbName.".";
        return(1);
    }

    //User name and password for http authentication
    /*
    * @param $c
    */
	function create_geoclass_user(&$c)
    {
    	foreach($c as $key => $value)
        {
        	$query .= ($query) ? '&c['.trim($key).']='.trim($value) : 'c['.trim($key).']='.trim($value);
        }

        $ch = curl_init();
        $url					= "http://".$this->conf['mi_host'].'/'.$this->subdomain."/register.php";
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt'); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);


		// MLC 20080109 ignore empty results
        if( curl_errno($ch)
			|| stristr($postResult, 'Congratulations!') === FALSE
			&& ( '' != $postResult )
		)
        {
            curl_close($ch);
            $this->errors['geouser'] = "Can't create Geosystem user.";
            $this->errors['geouser'] .= "\nurl\n$url";
            $this->errors['geouser'] .= "\nquery\n$query";
            $this->errors['geouser'] .= "\nresult\n$postResult";
            return(0);
        }

        curl_close($ch);
        $this->answers['geouser'] = "User has been created successfully.";
        return(1);
    }

    /*
    * @param $doSubdomain boolean false do basesite API loading
    */
    function add_subdomain_to_api( $doSubdomain = true )
    {
    	$c['installation_name'] = $doSubdomain
			? $this->subdomain.$this->conf['postfix']
    		: $this->conf['baseSite'];
    	$c['db_host'] = $doSubdomain
			? 'localhost'
    		: $this->conf['api_db_host'];
    	$c['db_username'] = $doSubdomain
			? $this->prefix.$this->userName
    		: $this->conf['api_db_username'];
    	$c['db_password'] = $doSubdomain
			? $this->userPassword
    		: $this->conf['api_db_password'];
    	$c['db_name'] = $doSubdomain
			? $this->prefix.$this->dbName
    		: $this->conf['api_db_name'];
    	$c['cookie_path'] = '';
    	$c['cookie_domain'] = $doSubdomain
			? $this->subdomain.$this->conf['postfix']
    		: $this->conf['baseSite'];
    	$c['installation_type'] = '1';
    	$c['admin_email'] = $this->conf['api_email'];
    	$c['vbulletin_config_path'] = '';
    	$c['vbulletin_license_key'] = '';
    	$c['phorum_database_table_prefix'] = '';
    	$c['cerberus_directory_path'] = '';
    	$c['cerberus_publicgui_path'] = '';
    	foreach($c as $key => $value)
    	{
        	if(!empty($value)) $cStr .= ($cStr) ? '&c['.$key.']='.$value : 'c['.$key.']='.$value;
    	}
        $ch = curl_init();
		$adminUrl				= 'http://'.$this->subdomain.'.'.$this->conf['mi_host'].'/admin/index.php';
		$loginUrl				= 'b[username]=miadmin&b[password]=H0iB6k';
        curl_setopt($ch, CURLOPT_URL, $adminUrl);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $loginUrl);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $store = curl_exec($ch);
		// cbDebug( 'admin', $adminUrl . '?' . $loginUrl );	

		$apiUrl					= 'a=100002&' . $cStr;
        curl_setopt($ch, CURLOPT_URL, $adminUrl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $apiUrl);
        $content = curl_exec ($ch);
		// cbDebug( 'admin', $adminUrl . '?' . $apiUrl );	

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['api'] = "Can\'t add site ".$c['installation_name']." to API.";
            return(0);
        }
        curl_close($ch);
        $this->answers['api'] = "<b>Site added to API.</b> Site ".$c['installation_name']." has been added to API successfuly.";
        return(1);
    }

// New functions
    /*
    * @param                                           
    */
    function del_db()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/sql/deldb.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, "db=".$this->prefix.$this->dbName);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['del_db'] = "Can't delete DB ".$this->prefix.$this->dbName.".";
            return(0);
        }
        curl_close($ch);
        if(stristr($postResult, 'Deleted the database') === FALSE) return(0);
        $this->answers['delDB'] = "DB has been deleted successfully.";
        return(1);
    }

    /*
    * @param
    */
    function del_user()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/sql/deluser.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=".$this->prefix.$this->userName);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['del_user'] = "Can't delete User ".$this->prefix.$this->userName.".";
            return(0);
        }
        curl_close($ch);
        if(stristr($postResult, 'Deleted the user') === FALSE) return(0);
        $this->answers['delUser'] = "User has been deleted successfully.";
        return(1);
    }

    /*
    * @param
    */
    function del_subdomain()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/subdomain/dodeldomain.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, "domain=".$this->subdomain."_".$this->conf['mi_host']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['del_subdomain'] = "Can't delete Subdomain ".$this->subdomain.".";
            return(0);
        }
        curl_close($ch);
        if(stristr($postResult, 'SubDomain Removal') === FALSE) return(0);
        $this->answers['SubDomain'] = "SubDomain has been deleted successfully.";
        return(1);
    }

    /*
    * @param
    */
    function del_subdomain_folder()
    {
		$exec					= 'rm -rf ' 
									. $this->conf['webdirectory']
									. $this->subdomain;
		shell_exec( $exec );
        $this->answers['del_subdomain_folder'] = "SubDomain folder has been deleted successfully.";
		return true;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/files/trashit.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, "dir=".$_SERVER['DOCUMENT_ROOT'].'&file='.$this->subdomain);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['del_subdomain_folder'] = "Can't delete Subdomain folder ".$this->subdomain.".";
            return(0);
        }

        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/files/trashit.html");
        $postResult1 = curl_exec($ch);

        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/files/dumpit.html");
        $postResult1 = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['del_subdomain_folder'] = "Can't delete Subdomain folder ".$this->subdomain.".";
            return(0);
        }
        curl_close($ch);
        $this->answers['SubDomainFolder'] = "SubDomain folder has been deleted successfully.";
        return(1);
    }

    /*
    * @param
    */
    function del_api($db)
    {
    	$query = "SELECT installation_id
		FROM `geodesic_api_installation_info`
		WHERE  installation_name = '".$this->subdomain.$this->conf['postfix']."'
		LIMIT 1";
        $result = mysql_query($query, $db);
        if($result)
        {
        	$row = mysql_fetch_array($result);
            $id = $row['installation_id'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseSite."/admin/index.php");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'b[username]=miadmin&b[password]=H0iB6k');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $store = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['DelAPI'] = "Can't delete API installation ".$this->subdomain.$this->conf['postfix'].".";
            return(0);
        }

        curl_setopt($ch, CURLOPT_URL, $this->baseSite."/admin/index.php");
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'a=100003&b='.$id);
        $content = curl_exec ($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['DelAPI'] = "Can't delete API installation ".$this->subdomain.$this->conf['postfix'].".";
            return(0);
        }
        curl_close($ch);
        $this->answers['api'] = "Site ".$this->subdomain.$this->conf['postfix']." has been deleted from API successfuly.";

        return(1);
    }

    function del_api_users($db) {
    	$query = "
			SELECT id
				, username
			FROM geodesic_userdata
			WHERE email LIKE '".$this->subdomain."%'
			LIMIT 1
		";
        $result = mysql_query($query, $db);
        if(!$result) {
            $this->errors['del_api_users'] = "Failed: $query";
			return;
		}

		$row = mysql_fetch_array($result);
		$id = $row['id'];
		$username = $row['username'];

		$query = "
			UPDATE geodesic_userdata
			SET email = CONCAT('x',email)
			WHERE id = $id
		";
        $result = mysql_query($query, $db);
        if($result) {
            $this->answers['del_api_users'] = "API User Email archived: $query";
		} else {
            $this->errors['del_api_users'] = "Failed: $query";
			return;
		}

		$query = "
			UPDATE geodesic_logins
			SET username = CONCAT('x',username)
			WHERE id = $id
		";
        $result = mysql_query($query, $db);
        if($result) {
            $this->answers['del_api_users'] = "API User Username archived: $query";
		} else {
            $this->errors['del_api_users'] = "Failed: $query";
			return;
		}
    }

    /*
    * @param $dir
    */
    function show_db_users($userName, &$res)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/sql/index.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            $this->errors['DbUserName'] = "This username already exist.";
            echo $this->errors['DbUserName'] . ': ' . $userName;
            return(0);
        }

        curl_close($ch);
        if(preg_match("/\"deluser\.html\?user=$userName\"/i", $postResult)) $res = 1;
        else $res = 0;

        if(!$res) $this->answers['dbUser'] = "$userName is an unique dbUser.";
        return(1);
    }

    /*
    * @param $dir
    */
    function create_ftp_account($dir, $password, $quota='unlimited')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
        curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/ftp/doaddftp.html");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, "login=".$this->subdomain."_".substr($dir, 0, 1)."&password=".$password."&quota=".$quota."&homedir=/".$dir."/".$this->subdomain);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $postResult = curl_exec($ch);

        if(curl_errno($ch))
        {
            curl_close($ch);
            //$this->errors['NewDir'] = "This username already exist.";
            return(0);
        }

        curl_close($ch);

        return(1);
    }

    /**
	 * Delete email fowards
     * @param 
     */
	function delete_email_forward($inEmail, $emailBase, $forwardToEmail)
    {
		$forwardToEmail = urlencode( $forwardToEmail );
   		$ch = curl_init();
   		curl_setopt($ch, CURLOPT_USERPWD, "$this->logpass");
   		curl_setopt($ch, CURLOPT_URL, "{$this->cpanelUrl}/mail/dodelfwd.html");
   		curl_setopt($ch, CURLOPT_POST, 1 );
   		curl_setopt($ch, CURLOPT_POSTFIELDS, "email=$inEmail@$emailBase=$forwardToEmail&emaildest=$forwardToEmail");
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   		$postResult = curl_exec($ch);

   		if(curl_errno($ch))
    	{
            curl_close($ch);
            $this->errors['delete_email_forward'] = $inEmail . ' unable to delete forward';
            return(0);
   		}
   		curl_close($ch);
		if(stristr($postResult, 'mail will no longer be redirected') === FALSE) return(0);
        $this->answers['delete_email_forward'] = $inEmail . ' email forward deleted';
		return(1);
    }
}
?>
