<?php
/**
 * odbc operation class
 * designed by busywarrior
 * */
class ODBC{
	private $host='';
	private $user='';
	private $password='';
	private $dbname='';
	
	public $conn=null;
	
	static $odbc=null;
	
	private function __construct($host,$user,$password,$dbname){
		$this->host = $host;
		$this->password = $password;
		$this->user = $user;
		$this->dbname = $dbname;
	}
	
	public static function getInstance(){
		if (self::$odbc == null){
			$config = include ROOT.'/odbc_config.php';// need to be customized
			self::$odbc = new odbc($config['host'],$config['user'],$config['password'],$config['dbname']);
		}
		return self::$odbc;
	}
	
	private function openConn($dbname=''){
	   $obj = &$this;
	   if (empty($dbname))$dbname = $obj->dbname;
       if($obj->conn == null)
       {
       		$obj->conn=odbc_connect("DRIVER={SQL Server};Server=".$obj->host.";Database=".$dbname.";",$obj->user,$obj->password);
       		if ($obj->conn){
       			return $obj->conn;
       		}
       }
       return false;
	}
	
	private function closeConn(){
		$obj = &$this;
		if ($obj->conn){
			odbc_close($obj->conn);
			$obj->conn = null;
		}
	}
	
	public function getAll($query,$dbname=''){
		$obj = &$this;
		$obj->openConn($dbname);
		$res = odbc_exec($obj->conn,$query) or die(odbc_errormsg($obj->conn));
		if ($res && odbc_num_rows($res)>0){
			$new = array();
			while($row=odbc_fetch_array($res)){
				$new[]=&$row;
			}
			unset($row);
		}
		odbc_free_result($res);
		$obj->closeConn();
		return $new;
	}
	
	public function getOne($query,$dbname=''){
		$obj = &$this;
		$obj->openConn($dbname);
		$res = odbc_exec($obj->conn,$query) or die(odbc_errormsg($obj->conn));
		if ($res && odbc_num_rows($res)>0){
			$new = array();
			if($row=odbc_fetch_array($res)){
				$new[]=&$row;
			}
			unset($row);
		}
		odbc_free_result($res);
		$obj->closeConn();
		return $new;
	}
	
	//for insert,update,delete
	public function query($query,$dbname=''){
		$obj = &$this;
		$obj->openConn($dbname);
		$res = odbc_exec($obj->conn,$query) or die(odbc_errormsg($obj->conn));
		odbc_free_result($res);
		$obj->closeConn();
		if ($res)return true;
		else return false;
	}
}