<?php
require_once "../functions.php";
$rsArticulos = $dbc->query("
SELECT
v.Descripcion
FROM articulosxdocumento a
JOIN vw_articulosdescritos v ON a.idarticulo = v.idarticulo
GROUP BY a.idarticulo

")
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<meta http-equiv="Content-Type"	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas</title>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
<h1>Llantera Esquipulas</h1>

<h2>Existencias de Productos por Bodega</h2>
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
</body>
</html>