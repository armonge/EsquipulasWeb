<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para a&ntilde;adir proveedores");
}
$status = "adding";
$id = (int)$_GET["id"];
if(!$id){
	header("Location: 404.php");
	die();
}
$query = "
SELECT p.idpersona, p.nombre, p.telefono,p.ruc, p.email
FROM personas p
LEFT JOIN personasxdocumento pxd ON pxd.idpersona = p.idpersona
WHERE p.idpersona = $id AND p.tipopersona = 2
";
$rsDetails = $dbc->query($query);
$row_rsDetails = $rsDetails->fetch_array(MYSQLI_ASSOC);

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
#m6 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m6 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
	<div id="left-column">
		<h1>Editar Proveedor</h1>
		<?php echo $error ?>
		<?php if($status == "success"){?>
			<p>El proveedor se a&ntilde;adio exitosamente</p>
		<?php }elseif($status == "adding"){ ?>
		<form class="cforms" method="post" action="providers/add.php">
		<p>
			<label>
				<span>Nombre</span>
				<input type="text" class="required" name="name" value="<?php echo $row_rsDetails["nombre"]?>" />
			</label>
		</p>
		<p>
			<label>
				<span>RUC / Identificacci&oacute;n</span>
				<input type="text" class="required" name="ruc" value="<?php echo $row_rsDetails["ruc"] ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>E-mail</span>
				<input type="text" class="required email" name="mail" value="<?php echo $row_rsDetails["email"] ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>Tel&eacute;fono</span>
				<input type="text" class="required number" name="phone" value="<?php echo $row_rsDetails["telefono"] ?>" />
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