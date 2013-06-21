<?php

class DB
{
	var $host = '';
    var $user = '';
    var $password = '';
    var $database = '';
    var $persistent = false;

    var $conn = NULL;
    var $result = false;

    function DB($host, $user, $password, $database, $persistent = false)
    {
    	$this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->persistent = $persistent;
    }

    function open()
    {
    	if($this->persistent) $func = 'mysql_pconnect';
        else $func = 'mysql_connect';

    	$this->conn = $func($this->host, $this->user, $this->password);
        if(!$this->conn) return false;
        if(!mysql_select_db($this->database, $this->conn)) return false;

        return true;
    }

    function close()
    {
    	return(@mysql_close($this->conn));
    }

    function error()
    {
    	return(mysql_error());
    }

    function query($sql)
    {
        $this->result = mysql_query($sql, $this->conn);
        return($this->result != false);
    }

    function fetchRow()
    {
    	return(@mysql_fetch_array($this->result));
    }

    function affectedRows()
    {
        return(@mysql_affected_rows($this->conn));
    }

    function numRows()
    {
    	return(@mysql_num_rows($this->result));
    }

    function freeResult()
    {
    	return(@mysql_free_result($this->result));
    }
}

?>