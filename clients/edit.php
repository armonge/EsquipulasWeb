<?php
/**
* class UserFromPasswd
* @package crm
* @author Andrés Reyes Monge <armonge@gmail.com>
*/
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para editar clientes");
}
$status = "edit";
if(isset($_POST["edit"])){
    $id = (int)$_POST["id"];
    if( $id){
        $name = $dbc->real_escape_string($_POST["name"]);
        $ruc = $dbc->real_escape_string($_POST["ruc"]);
        $mail = $dbc->real_escape_string($_POST["mail"]);
        $phone = $dbc->real_escape_string($_POST["phone"]);

        $dbc->simple_update("personas", array(
            "nombre" => $_POST["name"],
            "telefono" => $_POST["phone"],
            "email" => $_POST["mail"],
            "ruc" => $_POST["ruc"]
        ), array(
            "idpersona" => $id
        ));
        $status = "success";
    }
}
$id = (int)$_GET["id"];
if(!$id){
	header("Location: 404.php");
	die();
}
$query = "
SELECT p.idpersona, p.nombre, p.telefono,p.ruc, p.email
FROM personas p
LEFT JOIN personasxdocumento pxd ON pxd.idpersona = p.idpersona
WHERE p.idpersona = $id AND p.tipopersona = 1
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
		<h1>Editar Cliente</h1>
		<?php echo $error ?>
		<?php if($status == "success"){?>
			<p>El Cliente se edito exitosamente</p>
		<?php } ?>
		<form class="cforms" method="post" action="clients/edit.php?id=<?php echo $id ?>">
		<p>
			<label>
				<span>Nombre</span>
				<input type="text" class="required" name="name" value="<?php echo utf8tohtml($row_rsDetails["nombre"]) ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>RUC / Identificacci&oacute;n</span>
				<input type="text" class="required" name="ruc" value="<?php echo utf8tohtml($row_rsDetails["ruc"]) ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>E-mail</span>
				<input type="text" class="required email" name="mail" value="<?php echo utf8tohtml($row_rsDetails["email"]) ?>" />
			</label>
		</p>
		<p>
			<label>
				<span>Tel&eacute;fono</span>
				<input type="text" class="required number" name="phone" value="<?php echo utf8tohtml($row_rsDetails["telefono"]) ?>" />
			</label>
		</p>
		<p>
			<input type="submit" value="Aceptar" />
			<input type="hidden" value="yes" name="edit" />
			<input type="hidden" value="<?php echo $row_rsDetails["idpersona"] ?>" name="id" />
		</p>
		</form>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
