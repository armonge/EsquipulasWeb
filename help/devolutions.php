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
<?php include "../header.php"?>
<div id="content">
	<div id="left-column">
		<h1>Ayuda: Devoluciones</h1>

		<p>
			Permite realizar devoluciones de compras por productos en mal estado a
			clientes que lo requieran. Estas solo se pueden realizar con autorizaci&oacute;n
			de la gerencia en base a una factura existente, para poder realizar
			la devoluci&oacute;n de los articulos ya sea total o parcial de esa factura.
			</p>
	</div>
	<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>