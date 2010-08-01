<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para administrar usuarios");
}
$task = "";

if((isset($_POST["pwd"])) && ($_POST["pwd"]=="change")){
	//cambiar la contraseña
	$uid = (int)$_POST["uid"];
	if (($_POST["pwd1"] == $_POST["pwd2"]) && $uid){
		$pwd = AbstractUser::createPasswd( $dbc->real_escape_string($_POST["pwd1"]) );
		$dbc->query("UPDATE usuarios SET password = '$pwd' WHERE idusuario = $uid LIMIT 1" );
	}else{
		$error = "Las contrase&ntilde;as no coinciden";
	}

}else if( isset($_POST["rolchange"])  ){
	//cambiar los roles de un usuario
	if( count($_POST["roles"]) > 0){
		$uid = (int)$_POST["uid"];
		if($uid){
			$dbc->query("DELETE FROM usuarios_has_roles WHERE idusuario = $uid ");
			foreach($_POST["roles"] as $rol){
				if((int)$rol){
					$dbc->query("INSERT INTO usuarios_has_roles VALUES ( $uid, $rol )");
				}
			}
		}
	}else{
		$error = "No se pudo realizar el cambio por que: 'Un usuario deberia de tener por lo menos un permiso asignado'";
	}

}else if( isset( $_POST["add"] ) && ( $_POST["add"]="yes" ) ){
	//añadir un usuario
	$name = $_POST["nombre"] != ""  ? trim($dbc->real_escape_string($_POST["nombre"]))  : false ;
	$phone = $_POST["phone"] != ""  ? trim($dbc->real_escape_string($_POST["phone"]))  : false ;
	$uname = $_POST["uname"] != "" ? trim($dbc->real_escape_string($_POST["uname"])) : false;
	$pwd = $_POST["pwd1"] == $_POST["pwd2"] ? $dbc->real_escape_string($_POST["pwd1"]) : false;
	$roles = count($_POST["roles"]) > 0 ? $_POST["roles"] : false;

	if(!$name){
		$error =" No introdujo un nombre";
		$task = "new";
	}elseif(!$phone){
		$error = "No introdujo el telef&oacute;no del usuario";
		$task="new";
	}elseif(!$uname){
		$error = "No introdujo un nombre de usuario";
		$task = "new";
	}elseif(!$pwd){
		$error = "Existe un error con sus contrase&ntilde;as";
		$task = "new";
	}elseif(!$roles){
		$error = "No selecciono ning&uacute;n permiso para el usuario";
		$task = "new";
	}else{
		$pwd = AbstractUser::createPasswd($pwd);
		//insertar la persona
		$query ="INSERT INTO personas  (nombre, fechaingreso, telefono, tipopersona)
		VALUES('$name', CURDATE(), '$phone', 4)";
		echo $query;
		$dbc->query($query);

		$insertedId = $dbc->insert_id;

		//insertar el usuario
		$query = "
		INSERT INTO usuarios (idusuario,username,  password, estado, tipousuario)
		VALUES ( $insertedId, '$uname',  '$pwd', 1, 1)
		";
		echo $query;
		$dbc->query($query);

		$c = 0;
		foreach ($roles as $role){
			$rolId = (int)$role;
			if($rolId){
				$rs = $dbc->query("INSERT INTO usuarios_has_roles (idusuario, idrol) VALUES ($insertedId, $rolId)");
				if($rs){
					$c +=1;
				}
			}
		}
		if(!$c){
			$dbc->query("DELETE FROM usuarios WHERE idusuario = $insertedId LIMIT 1");
		}
	}
}
//lo que se va a mostrar en la vista
if(isset($_GET["task"])){
	$task = $task == "" ? $_GET["task"] : $task;
}

switch($task){
	case "del":
		$uid = (int)$_GET["uid"];
		$dbc->query("UPDATE usuarios SET estado=0 WHERE idusuario = $uid LIMIT 1");
	case "list":
		$rsUsers = $dbc->query("
		SELECT u.idusuario, p.nombre, u.username
		FROM usuarios u
		JOIN personas p ON p.idpersona = u.idusuario
		WHERE u.estado = 1
		");
		$rsUserRoles = $dbc->query("
		SELECT uhr.idusuario, uhr.idrol
		FROM  usuarios_has_roles uhr
		");
		$users = $rsUsers->fetch_all(MYSQLI_ASSOC);
		$userRoles = $rsUserRoles->fetch_all(MYSQLI_ASSOC);
	case "new":
		$rsRoles = $dbc->query("SELECT descripcion, idrol FROM roles");
		break;
	default:
		break;
}

?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript">
$(function(){
	$(".pwd").click(function(event){
		$("#fpwd"+$(this).attr("id").slice(4)).toggle()
		event.preventDefault();

	});
	$(".roles").click(function(event){
		$("#froles"+$(this).attr("id").slice(6)).toggle()
		event.preventDefault();

	});
});
</script>
<style type="text/css">
#m3 a {
	background: url(img/nav-left.png) no-repeat left;
}

#m3 span {
	background: #99AB63 url(img/nav-right.png) no-repeat right;
}

.permissions {
	display: inline-block;
}
</style>


</head>
<body>
<div id="wrap"><?php include "../header.php"?>
<div id="content">
<div id="left-column">
<h1>Administraci&oacute;n de Usuarios</h1>
<?php echo "<div class='error'>$error</div>" ?> <?php if (!$task){ ?>
<ul>
	<li><a href="administration/users.php?task=list"> Lista de Usuarios</a></li>
	<li><a href="administration/users.php?task=new">Crear un nuevo usuario</a></li>
</ul>
<?php }else if($task=="list"){
	$roles = $rsRoles->fetch_all(MYSQLI_ASSOC);
	?>
<h2>Lista de usuarios</h2>
	<?php if($users){ ?>
<ul>
<?php
foreach ($users as $user){
	?>
	<li><strong><?php echo $user["nombre"]?> ( <?php echo $user["username"]?>
	)</strong> <br />
	<a onclick=" return confirm('Seguro que desea borrar el usuario?')"
		class="del"
		href="administration/users.php?task=del&amp;uid=<?php echo $user["idusuario"] ?>">Borrar</a>
	/ <a class="pwd" id="apwd<?php echo $user["idusuario"] ?>" href="#">Cambiar
	Contraseña</a> / <a class="roles"
		id="aroles<?php echo $user["idusuario"] ?>" href="#">Cambiar Permisos</a>
	<form class="cforms hide" method="post"
		id="fpwd<?php echo $user["idusuario"] ?>"
		action="./administration/users.php?task=list">
	<p><label><span>Contrase&ntilde;a</span><input type="password"
		name="pwd1" /></label></p>
	<p><label><span>Repita la Contrase&ntilde;a</span><input
		type="password" name="pwd2" /></label></p>
	<p><input type="submit" value="Aceptar" /> <input type="hidden"
		name="uid" value="<?php echo $user["idusuario"]?>" /> <input
		type="hidden" name="pwd" value="change" /></p>
	</form>
	<form class="cforms hide" method="post"
		id="froles<?php echo $user["idusuario"] ?>"
		action="./administration/users.php?task=list">
		<?php foreach($roles as $rol){

			?>
	<p><label> <span><?php  echo $rol["descripcion"] ?></span> <input
		type="checkbox" name="roles[]" value="<?php echo $rol["idrol"] ?>"
		<?php
		foreach($userRoles as $urol){
			if($urol["idusuario"] == $user["idusuario"]){
				if ($rol["idrol"] == $urol["idrol"]){
					echo "checked='checked'";
				}
			}
		} ?> /> </label></p>
		<?php } ?>
	<p><input type="submit" value="Aceptar" /> <input type="hidden"
		name="uid" value="<?php echo $user["idusuario"] ?>" /> <input
		type="hidden" name="rolchange" value="yes" /></p>
	</form>
	</li>
	<?php }  ?>
</ul>
	<?php }else{ ?>
<p>No hay usuarios</p>
<?php } ?> <?php }else if($task == "new"){ ?>
<h2>Crear un nuevo Usuario</h2>
<form action="administration/users.php?task=list" method="post"
	class="cforms">
<p><label><span>Nombre Completo</span><input type="text" name="nombre"
	value="<?php echo $_POST["nombre"]?>" /></label></p>
<p><label><span>Telef&oacute;no</span><input type="text" name="phone"
	value="<?php echo $_POST["phone"]?>" /></label></p>
<p><label><span>Usuario</span><input type="text" name="uname"
	value="<?php echo $_POST["uname"]?>" /></label></p>
<p><label><span>Contrase&ntilde;a</span><input type="password"
	name="pwd1" /></label></p>
<p><label><span>Repita la Contrase&ntilde;a</span><input type="password"
	name="pwd2" /></label></p>
<h3>Permisos</h3>
<?php while($row_rsRol = $rsRoles->fetch_assoc()){ ?>
<p class="permissions"><label> <span><?php echo $row_rsRol["descripcion"] ?></span>
<input type="checkbox" name="roles[]"
	value="<?php echo $row_rsRol["idrol"] ?>" /> </label></p>
<?php }?>
<p><input type="hidden" name="add" value="yes" /> <input type="submit"
	value="Aceptar" /></p>
</form>
<?php } ?></div>
</div>
<?php include "../footer.php" ?></div>
</body>
</html>
