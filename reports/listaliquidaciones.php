<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("gerencia") || $_SESSION["user"]->hasRole("contabilidadrep"))){
    die("Usted no tiene permisos para este modulo");
}
$query = "
SELECT
    d.iddocumento,
    d.ndocimpreso AS 'ndocimpreso',
    d.fecha AS 'Fecha',
    d.Proveedor AS 'Proveedor',
    d.bodega AS 'Bodega',
    d.totald AS 'Total US$',
    d.totalc AS 'Total C$'
FROM esquipulasdb.vw_liquidacionesguardadas d
GROUP BY d.iddocumento;
";
$rsLiquidaciones = $dbc->query($query);

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
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript">
$(function(){
});
</script>
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
    <?php echo $print ?>
    <h1>Liquidaci&oacute;n de costos</h1>
     <?php if($rsLiquidaciones->num_rows){ ?>
    <ul>
         <?php while($row_rsDocument = $rsLiquidaciones ->fetch_array(MYSQLI_ASSOC)){ ?>
            <li><a href="<?php echo $base ?>reports/liquidaciones.php?doc=<?php echo $row_rsDocument["iddocumento"] ?>"><?php echo $row_rsDocument["Fecha"] ?> para <?php echo $row_rsDocument["Proveedor"] ?></a></li>
         <?php } ?>
    </ul>
     <?php } ?>

<form action="reports/liquidaciones.php" method="get" >
<p>
<strong>Escriba el numero de Liquidacion a buscar:</strong>

<input type="text" name="doc" size="20" /><br /><br />

<input type="submit" value="Buscar" />
</p>
</form>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>