<?php

class phpMysql_src_pdo_h
{
	var $listmysql="";								
	var $sql="";
	var $linkmysql=0;
	
	var $ip = "192.168.1.123";
	var $db = "mge";							
	var $user = "cms";
	var $password="123456";
	
	var $dbh = NULL;
	
	function __destruct()
	{
		$this->dbh = NULL;
	}

	function link_mysql()							
	{
		try {
			$this->dbh = new PDO("mysql:host=".$this->ip.";dbname=".$this->db, $this->user, $this->password);
			//$this->dbh->exec("SET CHARACTER SET utf8");
			$sql = "SET NAMES utf8";
			$sth = $this->dbh->prepare($sql);
			$sth->execute();
			//$this->dbh->exec("SET NAMES utf8");
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch( PDOException $Exception ) {
			echo $Exception->getMessage().$Exception->getCode();
			exit;
		}
	}

	function init()
	{
		$this->link_mysql();
	}
	
	function add($table,$Field,$valuex)
	{
		$sql = "INSERT INTO ".$table." (".$Field.")  VALUES  (".$valuex.")";
		
		$sth = $this->dbh->prepare($sql);	
		$sth->execute();
		
        return $this->dbh->lastInsertId(); 
	}

}


function loaddata_row($table,$where_str="",$usedb="",$where_val=array())  //
{
	$chkispowermsql = new phpMysql_src_pdo_h;
	if ($usedb!="") 
	{
		$chkispowermsql->db = $usedb;
	}
	$chkispowermsql->init();
	$sql = "SELECT * FROM ".$table." ".$where_str.";"; //echo $sql;
	$sth = $chkispowermsql->dbh->prepare($sql);
	
	if (count($where_val)!=0) {
		$sth->execute($where_val);
	}else {
		$sth->execute();
	}
	//
	$row=NULL;
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	
	unset($chkispowermsql);

	return $row;
}

// advance loading
function loaddata_row_adv($columns,$table,$where_str="",$usedb="",$where_val=array())  //
{
	$chkispowermsql = new phpMysql_src_pdo_h;
	if ($usedb!="") 
	{
		$chkispowermsql->db = $usedb;
	}
	$chkispowermsql->init();
	$sql = "SELECT ".$columns." FROM ".$table." ".$where_str.";"; //echo $sql;
	$sth = $chkispowermsql->dbh->prepare($sql);
	
	if (count($where_val)!=0) {
		$sth->execute($where_val);
	}else {
		$sth->execute();
	}
	//
	$row=NULL;
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	
	unset($chkispowermsql);

	return $row;
}

function loaddata_array($table,$where_str="",$usedb="",$where_val=array())  //
{
	$chkispowermsql = new phpMysql_src_pdo_h;
	if ($usedb!="") 
	{
		$chkispowermsql->db = $usedb;
	}
	$chkispowermsql->init();
	$sql = "SELECT * FROM ".$table." ".$where_str.";"; //echo $sql;
	$sth = $chkispowermsql->dbh->prepare($sql);
	
	if (count($where_val)!=0) {
		$sth->execute($where_val);
	}else {
		$sth->execute();
	}
	//
	$j=0;
	$r=NULL;
	/*while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		$r[$j] = $row;
		 $j++;
	}*/
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	unset($chkispowermsql);

	return $r;
}

// advance loading
function loaddata_array_adv($columns,$table,$where_str="",$usedb="",$where_val=array())  //
{
	$chkispowermsql = new phpMysql_src_pdo_h;
	if ($usedb!="") 
	{
		$chkispowermsql->db = $usedb;
	}
	$chkispowermsql->init();
	$sql = "SELECT ".$columns." FROM ".$table." ".$where_str.";"; //echo $sql;
	$sth = $chkispowermsql->dbh->prepare($sql);
	
	if (count($where_val)!=0) {
		$sth->execute($where_val);
	}else {
		$sth->execute();
	}
	//
	$j=0;
	$r=NULL;
	/*while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		$r[$j] = $row;
		 $j++;
	}*/
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	unset($chkispowermsql);

	return $r;
}

function load_data($sql,$usedb="",$where_val=array())
{
	$chkispowermsql = new phpMysql_src_pdo_h;
	if ($usedb!="") 
	{
		$chkispowermsql->db = $usedb;
	}
	$chkispowermsql->init();
	$sth = $chkispowermsql->dbh->prepare($sql);
	
	if (count($where_val)!=0) {
		$sth->execute($where_val);
	}else {
		$sth->execute();
	}
	//
	$j=0;
	$r=NULL;
	/*while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		$r[$j] = $row;
		 $j++;
	}*/
	$r = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	unset($chkispowermsql);

	return $r;
}

function sql_exe($sql,$usedb="",$where_val=array())
{
	$chkispowermsql = new phpMysql_src_pdo_h;
	if ($usedb!="") 
	{
		$chkispowermsql->db = $usedb;
	}
	$chkispowermsql->init();
	
	$sth = $chkispowermsql->dbh->prepare($sql);
	
	if (count($where_val)!=0) {
		$sth->execute($where_val);
	}else {
		$sth->execute();
	}
	
	unset($chkispowermsql);
}

function loadCount($table,$where_str="",$usedb="",$where_val=array())  //
{
	$chkispowermsql = new phpMysql_src_pdo_h;
	if ($usedb!="") 
	{
		$chkispowermsql->db = $usedb;
	}
	$chkispowermsql->init();
	
	$sql = "SELECT COUNT(*) as cs FROM ".$table." ".$where_str;
	
	$sth = $chkispowermsql->dbh->prepare($sql);
	
	if (count($where_val)!=0) {
		$sth->execute($where_val);
	}else {
		$sth->execute();
	}
	
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	
	unset($chkispowermsql);

	return $row['cs'];
}

function member_cash_vpoint($m,$point,$doaction)
{
	$srcpoint = $m['vpoint'];
	$n = loaddata_row("memberInfo","WHERE uuid='".$m['uuid']."' ");
	$sql = "INSERT INTO cash_log_v (memId,srcpoint,point,descpoint,createDate,doaction) VALUES ('".$m['uuid']."','".$srcpoint."','".$point."','".$n['vpoint']."',NOW(),'".$doaction."') "; //echo $sql;exit;
	sql_exe($sql);
}

?>