<?php

include_once 'database_class.php';
	
class User extends Database {

	protected static $tableKey = array(
		'userId' => array('type' => DataType::NUMBER, 'sequence' => 'USERS_SEQUENCE')
	);

	protected static $tableAttributes = array(
		'USERS' => array(
			'username' => array('type' => DataType::VARCHAR),
			'password' => array('type' => DataType::VARCHAR)
		)
	);

	protected $userId;
	protected $username;
	protected $password;

	public function __construct ($key = array(), $select = false) {
		parent::__construct();

		foreach ($key as $name => $value) {
			if (array_key_exists($name, static::$tableKey)) {
				$this->{$name} = $value;
			}
		}

		if ($select) {
			$this->setAttributes($this->select());
		}
	}

	public function getUserId() { return $this->userId; }
	public function getUsername() { return $this->username; }

	public function setAttributes($attributes) {
		foreach($attributes as $name => $value) {
			if (!array_key_exists($name, static::$tableKey)) {
				$this->{$name} = $value;
			}
		}
	}	
	
	public function login() {
		try{
			$connection = Database::start();
			$sqlString = 'SELECT * 
						FROM USERS
						WHERE USERNAME = :username 
						and PASSWORD = :password';
			$stid = oci_parse($connection, $sqlString);
			oci_bind_by_name($stid, ':username', $this->username, 20);
			oci_bind_by_name($stid, ':password', $this->password, 20);
			
			oci_define_by_name($stid, 'USER_ID', $userid);
			oci_define_by_name($stid, 'USERNAME', $username);
			oci_define_by_name($stid, 'PASSWORD', $passwordHash);
			oci_execute($stid);

			while (oci_fetch($stid)) {
				if($userid != null){
					$this->id = $userid;
					$this->username = $username;
					$this->password = $passwordHash;
					
					session_start();
					$_SESSION['USER'] = $this;
					session_write_close();
					return;
				}
			}
			throw new Exception('Improper credentials supplied');
		}
		catch (Exception $exception) {
			if($connection != null){
				Database::end($connection);	
			}
			throw $exception;
		}
		if($connection != null){
			Database::end($connection);	
		}
		
	}
	
	public static function hash($p){
		$input=iconv('UTF-8','UTF-16LE',$p);
        return bin2hex(mhash(MHASH_MD4,$input));
	}

	public function getTotalProfit() {
		$amountOut = Game::sum('amountOut', array('userId' => $this->userId));
		$amountIn = Game::sum('amountIn', array('userId' => $this->userId));
		return $amountOut - $amountIn;
	}
}

?>