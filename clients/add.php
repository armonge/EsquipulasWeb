<?php
/**
* class UserFromPasswd
* @package crm
* @author Andrés Reyes Monge <armonge@gmail.com>
*/

require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para a&ntilde;adir proveedores");
}
$status = "adding";
if(isset($_POST["add"])){
	$name = $dbc->real_escape_string(trim($_POST["name"]));
	$ruc = $dbc->real_escape_string(trim($_POST["ruc"]));
	$phone = $dbc->real_escape_string(trim($_POST["phone"]));
	$email = $dbc->real_escape_string(trim($_POST["mail"]));

	if(!$name){
		$error="No especifico el nombre del Cliente";
	}elseif(!$ruc){
		$error="No escribio el RUC/Identificaci&oacute;n del Cliente";
	}elseif(!$phone){
		$error="No escribio el tel&eacute;fono del Cliente";
	}elseif(!$email){
		$error="No escribio el e-mail del Cliente";
	}else{
		$query = "INSERT INTO personas (nombre,fechaingreso, telefono, email, ruc, activo, tipopersona)
		VALUES('$name', CURDATE(), '$phone','$email', '$ruc', 1,1 )";
		if($dbc->query($query)){
			$status = "success";
		}else{
			$error = "Hubo un error al realizar la consulta, por favor comuniquese con su administrador";
		}

	}

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
<title>Llantera Esquipulas: Administraci&oacute;n</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.validate.js"></script>
<script type="text/javascript" src="js/messages_es.js"></script>
<script type="text/javascript">
$(function(){
	$("form").validate();
});
</script>
<style type="text/css">
#m5 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m5 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
	<div id="left-column">
		<h1>A&ntilde;adir Cliente</h1>
		<?php echo $error ?>
		<?php if($status == "success"){?>
			<p>El cliente se a&ntilde;adio exitosamente</p>
		<?php }elseif($status == "adding"){ ?>
		<form class="cforms" method="post" action="clients/add.php">
		<p>
			<label>
				<span>Nombre</span>
				<input type="text" class="required" name="name" value="<?php echo $_POST["name"] ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>RUC / Identificacci&oacute;n</span>
				<input type="text" class="required" name="ruc" value="<?php echo $_POST["ruc"] ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>E-mail</span>
				<input type="text" class="required email" name="mail" value="<?php echo $_POST["mail"] ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>Tel&eacute;fono</span>
				<input type="text" class="required number" name="phone" value="<?php echo $_POST["phone"] ?>" />
			</label>
		</p>
		<p>
			<input type="submit" value="Aceptar" />
			<input type="hidden" value="yes" name="add" />
		</p>
		</form>
		<?php } ?>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
