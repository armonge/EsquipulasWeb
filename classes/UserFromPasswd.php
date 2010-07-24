<?php
class UserFromPasswd extends AbstractUser{
	public function __construct($user, $password, $database){
		$this->dbc = $database;
		$this->uname = $this->dbc->real_escape_string($user);
		$this->password = $this->dbc->real_escape_string($password);

		$result = $this->dbc->query("
			SELECT u.idusuario AS uid,  r.nombre as rol FROM usuarios u
                JOIN usuarios_has_roles ur ON u.idusuario = ur.idusuario
                JOIN roles r ON r.idrol = ur.idrol
                WHERE u.estado = 1
                AND u.username = '{$this->uname}'
                AND u.password = SHA1('{$this->password}".self::$secret."')
		");
		
		if($result->num_rows > 0){
			$this->valid = True;
			while($row = $result->fetch_assoc()){
				$this->roles[] = $row["rol"];
				$this->uid = $row["uid"];
			}
		}


	}
}
?>