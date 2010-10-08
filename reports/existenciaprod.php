<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
$query = "
SELECT
    v.Descripcion,
    SUM(a.unidades) as existencia,
    b.nombrebodega as bodega
FROM documentos k
LEFT JOIN docpadrehijos dpd ON dpd.idhijo = k.iddocumento
LEFT JOIN documentos p ON p.iddocumento = dpd.idpadre
JOIN articulosxdocumento a ON a.iddocumento = k.iddocumento OR a.iddocumento = dpd.idpadre
JOIN bodegas b ON b.idbodega = k.idbodega
JOIN vw_articulosdescritos v ON a.idarticulo = v.idarticulo
WHERE k.idtipodoc = {$docids["KARDEX"]}
GROUP BY a.idarticulo, b.idbodega
HAVING existencia != 0
ORDER BY b.idbodega ASC, v.Descripcion
";
$rsArticulos = $dbc->query($query);
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
<h1>Llantera Esquipulas</h1>

<h2>Existencias de Productos por Bodega</h2>
<?php if(!$rsArticulos->num_rows){ ?>
    <p>No hay existencia en bodega</p>
<?php } else{ ?>
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1"	summary="Reporte de partida contable">
	<col width="400" />
	<thead>
	<tr>
		<th>Articulo</th>
		<th>Bodega</th>
		<th>Unidades</th>
	</tr>
	</thead>
	<tbody>
	<?php $color = 1; while ($row_rsArticulos = $rsArticulos->fetch_assoc() ){ $color++;  ?>
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td><?php echo $row_rsArticulos["descripcion"] ?></td>
		<td><?php echo $row_rsArticulos["bodega"] ?></td>
		<td><?php echo $row_rsArticulos["existencia"] ?></td>
	</tr>
	<?php } ?>

	</tbody>
</table>
<?php } ?>
</div>
<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>