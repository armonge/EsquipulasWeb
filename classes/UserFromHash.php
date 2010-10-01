<?php
/**
* class UserFromHash
* @package users
* @author Andrés Reyes Monge <armonge@gmail.com>
*/
class UserFromHash extends AbstractUser{
	public function __construct($user, $hash, $database){
		$this->dbc = $database;
		$this->uname = $this->dbc->real_escape_string($user);
		$this->hash = $this->dbc->real_escape_string($hash);

		$result = $this->dbc->query("
			SELECT u.idusuario AS uid,  r.nombre as rol FROM usuarios u
                JOIN usuarios_has_roles ur ON u.idusuario = ur.idusuario
                JOIN roles r ON r.idrol = ur.idrol
                WHERE u.estado = 1
               AND u.username = '{$this->uname}'
               AND SHA1( CONCAT(u.password, '".self::$secret."') ) = '{$this->hash}'
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