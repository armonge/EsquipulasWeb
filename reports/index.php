<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("contabilidadrep") || $_SESSION["user"]->hasRole("inventariorep") || $_SESSION["user"]->hasRole("ventasrep")|| $_SESSION["user"]->hasRole("gerencia")) ){
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
#content ul{
    list-style:none;
    width:90%;
    margin:auto;
    padding:0;
    text-align:center;
}
#content li{
    list-style-image:none;
    width:80px;
    margin:10px;
    text-align:center;
    display:inline-block;

    zoom:1;
    *display:inline;
}
#content li img{
	display:block;
	margin:auto;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
		<h1>Reportes</h1>
		<ul>
		<?php if($_SESSION["user"]->hasRole("contabilidadrep") || $_SESSION["user"]->hasRole("gerencia")) {?>
			<li>
				<a href="reports/accounts.php">
				<img src="img/office-chart-line.png" alt="Historico de cuentas" />
				Historico de Cuentas
				</a>
			</li>
			<li>
				<a href="reports/partidacontable.php">
				<img src="img/view-table-of-contents-rtl.png" alt="Reporte de partida contable" />
				Reporte de Partida Contable
				</a>
			</li>
			<li>
				<a href="reports/balancegeneral.php">
				<img src="img/view-table-of-contents-rtl.png" alt="Balance general" />
				Balance General
				</a>
			</li>
			<li>
				<a href="reports/balancecomprobacion.php">
				<img src="img/view-table-of-contents-rtl.png" alt="Balance de comprobaci&oacute;n" />
				Balance de Comprobaci&oacute;n
				</a>
			</li>
			<li>
				<a href="reports/listaliquidaciones.php">
				<img src="img/view-table-of-contents-rtl.png" alt="Liquidaci&oacute;n de costos" />
				Liquidacion de Costos
				</a>
			</li>

		<?php } if($_SESSION["user"]->hasRole("ventasrep") || $_SESSION["user"]->hasRole("gerencia")){ ?>
			<li>
				<a href="reports/articlessales.php">
				<img src="img/office-chart-line.png" alt="Ventas x producto" />
				Ventas x producto
				</a>
			</li>
			<li>
				<a href="reports/articlessalespie.php">
				<img src="img/office-chart-line.png" alt="5 productos m&aacute;s vendidos" />
				5 Productos m&aacute;s vendidos
				</a>
			</li>
			<li>
				<a href="reports/salesxclient.php">
				<img src="img/office-chart-pie.png" alt="Ventas x Persona" />
				Ventas x Persona
				</a>
			</li>

		<?php } if($_SESSION["user"]->hasRole("inventariorep") || $_SESSION["user"]->hasRole("gerencia")){ ?>
            <li>
            	<a href="reports/existenciaprod.php">
            		<img src="img/view-table-of-contents-rtl.png" alt="Existencia de productos" />
            	Existencia de productos
            	</a>
            </li>
            <li>
            	<a href="reports/articlesmoves.php">
            	<img src="img/office-chart-line.png" alt="Movimientos de productos" />
            	Movimientos de productos
            	</a>
            </li>
        
		<?php } if($_SESSION["user"]->hasRole("gerencia")){ ?>
            <li>
            	<a href="reports/articlescosts.php">
            	<img src="img/office-chart-line.png" alt="Costos x producto" />
            	Costos x producto
            	</a>
            </li>
            

		<?php } ?>

		
		</ul>
	<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>