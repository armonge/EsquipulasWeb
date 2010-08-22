<?php
require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("contabilidadrep") || $_SESSION["user"]->hasRole("inventariorep") || $_SESSION["user"]->hasRole("ventasrep"))){
	die("Usted no tiene permisos para ver reportes");
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
<title>Llantera Esquipulas: Reportes</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
#m2 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m2 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
	<div id="left-column">
		<h1>Reportes</h1>
		<ul>
		<?php if($_SESSION["user"]->hasRole("contabilidadrep")) {?>
			<li><a href="reports/accounts.php">Historico de Cuentas</a></li>
			<li><a href="reports/partidacontable.php">Reporte de Partida Contable</a></li>
			<li><a href="reports/balancegeneral.php">Balance General</a></li>
			<li><a href="reports/balancecomprobacion.php">Balance de Comprobaci&oacute;n</a></li>

		<?php } if($_SESSION["user"]->hasRole("ventasrep")){ ?>
			<li><a href="reports/articlessales.php">Ventas x producto</a></li>
			<li><a href="reports/salesxclient.php">Ventas x Persona</a></li>



		<?php } if($_SESSION["user"]->hasRole("inventariorep")){ ?>
			<li><a href="reports/articlescosts.php">Costos x producto</a></li>

		<?php } if($_SESSION["user"]->hasRole("inventariorep")){ ?>
			<li><a href="reports/listaliquidaciones.php">Liquidacion de Costos</a></li>


		<?php } ?>
		</ul>
	</div>
	<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>