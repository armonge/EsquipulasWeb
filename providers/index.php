<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para administrar proveedores");
}

$rsProviders = $dbc->query("
SELECT idpersona, nombre FROM personas p WHERE tipopersona = 2 AND activo = 1
")

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
<script type="text/javascript">
$(function(){

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
		<h1>Proveedores</h1>
		<?php echo $status ?>
			<?php if($rsProviders->num_rows){ ?>
			<ul>
				<?php while($row_rsProvider = $rsProviders->fetch_array(MYSQLI_ASSOC)){ ?>
					  <li>
					  	<a href="providers/details.php?id=<?php echo $row_rsProvider["idpersona"] ?>"><?php echo $row_rsProvider["nombre"]?></a>
					  </li>
				<?php } ?>
			</ul>
		<?php }else{ ?>
			<p>Todavia no ha a&ntilde;adido ning&uacute;n proveedor</p>
		<?php } ?>
		<a href="providers/add.php" >A&ntilde;adir Proveedor</a>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>