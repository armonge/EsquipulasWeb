<?php
abstract class AbstractUser{
	protected $uid;
	protected $roles = array();
	protected $valid = False;
	protected $dbc;
	protected static $secret = '7/46u23opA)P231popas;asdf3289AOP23';

	public $error ="";
	public $uname ="";
	public $password="";
	public $hash="";

	public abstract function __construct();

	public function getRoles(){
		return $this->roles;
	}

	public function isValid(){
		return $this->valid;
	}
	public function hasRole($role){
		if ($this->isValid()){
			if (in_array('root', $this->roles)){

				return True;
			}else{
				return in_array($role, $this->roles);
			}
		}
		return False;
	}
	public function getUid(){
		return $this->uid;
	}
	public function createPasswd($pwd){
		return sha1($pwd . self::$secret);
	}
}
?>