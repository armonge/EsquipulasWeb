<?php
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
<?php include "../header.php"?>
<div id="content">
	<div id="left-column">
		<h1>Ayuda: Catalogo de Articulos</h1>

		<p>
		Permite insertar articulos con sus respectivos valores de impuestos y % de ganancia para realizar la liquidaci&oacute;n de costos
		 y determinar el costo del mismo para calcular el precio de venta, entre los requerimientos para crear un articulo existen:
		 Las categorias y subcategorias que deben ser previamente creadas
		 </p>
		 <dl>
		  <dt>ej:</dt>
		 <dd>1 Articulo se conforma por una Llanta, pero su subcategoria
		 es <strong>11R22.5</strong> lo que que genera la descripcion del articulo <strong>"Llanta 11R22.5"</strong>
		</dd>
		</dl>
	</div>
	<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>