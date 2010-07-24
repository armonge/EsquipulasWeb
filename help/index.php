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
		<h1>Ayuda</h1>

		<p>
			Bienvenido al sistema de ayuda del sistema de informaci&oacute;n gerencial de
			<strong>Llantera Esquipulas</strong>, desde aqui usted encontrara toda la
			documentaci&oacute;n necesaria para utilizar de manera adecuada los distintos
			componentes del sistema.
		</p>
		<ul>
			<li>
				<h2>Inventario</h2>
				<ul>
					<li><a href="help/catarticles.php">Catalogo de articulos</a></li>
					<li><a href="help/catwarehouses.php">Catalogo de bodegas</a></li>
					<li><a href="help/catcategories.php">Catalogo de categorias</a></li>
					<li><a href="help/catsubcategories.php">Catalogo de subcategorias</a></li>
					<li><a href="help/catproviders.php">Catalogo de proveedores</a></li>
					<li><a href="help/liquidations.php">Liquidaciones</a></li>
					<li><a href="help/localentries.php">Compras Locales</a></li>
					<li><a href="help/devolutions.php">Devoluciones</a></li>
				</ul>
			</li>
		</ul>
	</div>
	<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>