<?php
require_once "../functions.php";
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para administrar vendedores");
}
$del = (int)$_GET["del"];
if($del){
	$dbc->query("UPDATE personas  SET activo = IF(activo = 0,1,0) WHERE idpersona = $del AND tipopersona = 3 LIMIT 1");
	if($dbc->affected_rows){
			$status="Se ha cambiado el estado de un vendedor";
	}
}
if(isset($_POST["add"])){
	$name = $dbc->real_escape_string(trim($_POST["name"]));
	$phone = $dbc->real_escape_string(trim($_POST["phone"]));
	$email = $dbc->real_escape_string(trim($_POST["mail"]));
	$ruc = $dbc->real_escape_string(trim($_POST["ruc"]));
	$address = $dbc->real_escape_string(trim($_POST["address"]));

	if(!$name){
		$error="No especifico el nombre del Vendedor";
	}elseif(!$phone){
//		FIXME: Que pasa cuando $phone == 0
		$error="No escribio el tel&eacute;fono del Vendedor";
	}elseif(!$email){
        $error="No escribio el e-mail del Vendedor";
    }elseif(!$address){
        $error="No escribio la direcci&oacute;n del Vendedor";
    }else{
		$query = "INSERT INTO personas (nombre,fechaingreso, telefono, email,ruc, direccion, activo, tipopersona)
		VALUES('$name', CURDATE(), '$phone','$email', '$ruc', '$direccion', 1,3 )";
		if($dbc->query($query)){
			$status = "success";
		}else{
			$error = "Hubo un error al realizar la consulta, por favor comuniquese con su administrador";
		}

	}
}

$rsSalesmen = $dbc->query("SELECT idpersona, nombre, activo FROM personas WHERE tipopersona = 3 ORDER BY activo DESC");
$row_rsSalesmen=$rsSalesmen->fetch_array(MYSQLI_ASSOC);
$estadoVendedores = $row_rsSalesmen["activo"];

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
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.validate.js"></script>
<script type="text/javascript" src="js/messages_es.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
#m3 a{
    background: url(img/nav-left.png) no-repeat left;
}
#m3 span{
    background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
#add{
	display:none;
}
</style>
<script type="text/javascript">
$(function(){
    $("#toggleadd").click(function(){
        $("#add").toggle();
        return false;
    });
    $("#add form").validate();
});
</script >
</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
	    <h1>Vendedores</h1>
		<?php echo $error ?>
	    <?php if($rsSalesmen->num_rows){ ?>
		<?php echo $estadoVendedores == 0 ?  "<p>Vendedores Inactivos</p>" :"" ; ?>
		<ul>
		<?php do{ 
				if($estadoVendedores != $row_rsSalesmen["activo"]){
						echo "</ul>Vendedores Inactivos<ul>";
				}
				$estadoVendedores = $row_rsSalesmen["activo"];
		?>
				<li><a href="administration/salesmandetails.php?id=<?php echo $row_rsSalesmen["idpersona"] ?>"><?php echo $row_rsSalesmen["nombre"] ?></a></li>
		<?php }while($row_rsSalesmen = $rsSalesmen->fetch_array(MYSQLI_ASSOC)) ?>
		</ul>
	    <?php }else{ ?>
		No hay ning&uacute;n vendedor
	    <?php } ?>
		<p>
			<a href="administration/salesmen.php?add=1" id="toggleadd" >A&ntilde;adir Vendedor</a>
		</p>
		<div id="add">
		<h1>A&ntilde;adir Vendedor</h1>
		<form class="cforms" method="post" action="administration/salesmen.php">
		<p>
			<label>
				<span>Nombre</span>
				<input type="text" class="required" name="name" value="<?php echo $_POST["name"] ?>" />
			</label>
		</p>
		<p>
			<label>
				<span> Identificaci&oacute;n</span>
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
            <label>
                <span>Direcci&oacute;n</span>
                <input type="text" class="required" name="address" value="<?php echo $_POST["address"] ?>" />
            </label>
        </p>
		<p>
			<input type="submit" value="Aceptar" />
			<input type="hidden" value="yes" name="add" />
		</p>
		</form>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
