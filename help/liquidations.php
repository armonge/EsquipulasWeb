<?php
/**
* class UserFromPasswd
* @package help
* @author Andrés Reyes Monge <armonge@gmail.com>
*/
$nologin = True;
require_once "../functions.php";
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas: Ayuda</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
#m4 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m4 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>


</head>
<body>
<div id="wrap">
<?php include "../menu.php"?>
<div id="content">
	<div id="left-column">
		<h1>Ayuda: Liquidaciones</h1>

		<p>
		 Permite ingresar articulos a Inventario los productos adquiridos por medio de
		 importaciones y calcular los costos para poder evaluar y analizar el precio de
		 venta que se le asignara a los productos.
		</p>
		<p>
			Para poder realizar una liquidacion de costos, los articulos deben
			existir en la BD con sus costos agregados, para poder realizar los
			calculos y determinar el precio de venta.
			</p>
	</div>
	<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>