<?php
require_once "../functions.php";

$iddoc = (int)$_GET["doc"];
if(!$iddoc){
	die();
}
$query="
SELECT
    padre.iddocumento,
    DAY(padre.fechacreacion) as 'dia',
    MONTH(padre.fechacreacion) as 'mes',
    YEAR(padre.fechacreacion) as 'anio',
    padre.ndocimpreso,
    p.nombre as 'cliente',
    padre.total as 'total',
    c.descripcion as 'concepto',
    ROUND(padre.total - IFNULL(hijo.total,0),2) as 'total',
    padre.observacion ,
	IF(hijo.iddocumento IS NULL, 0,1) as 'Con Retencion'
FROM documentos padre
JOIN personasxdocumento pxd ON pxd.iddocumento = padre.iddocumento
JOIN personas p ON p.idpersona = pxd.idpersona
JOIN conceptos c ON  c.idconcepto=padre.idconcepto
LEFT JOIN costosxdocumento cd ON cd.iddocumento=padre.iddocumento
LEFT JOIN  costosagregados ca ON ca.idcostoagregado=cd.idcostoagregado
LEFT JOIN docpadrehijos ph ON  padre.iddocumento=ph.idpadre
LEFT JOIN documentos hijo ON hijo.iddocumento=ph.idhijo
WHERE padre.idtipodoc={$docids["RECIBO"]}
AND p.tipopersona={$persontypes["CLIENTE"]}
AND padre.iddocumento = $iddoc
ORDER BY padre.iddocumento
";
$rsRecibo = $dbc->query($query);
$row_rsRecibo = $rsRecibo->fetch_array(MYSQLI_ASSOC);
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Llantera Esquipulas: Devoluci&oacute;n No <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<style type="text/css">
html {
	border: 0;
	margin: 0;
	padding: 0;
}

body {
	font-family: Arial;
	font-size: 12pt;
	width: 750pt;
	padding: 0;
	margin: 0;
	padding-left: 60pt;
	padding-top: 50pt;
	height: 960pt;
	color:#000;
	/* 	border:1px solid #000; */
}

h1 {
	text-align: center;
}

@media print {
}
</style>
</head>
<body>
<div id="header">
<h1>RECIBO <span id="docnumber">N° <?php echo $row_rsRecibo["ndocimpreso"] ?></span></h1>
<table border="1" rules="all" cellpadding="5" cellspacing="0" id="fecha">
	<thead>
		<tr>
			<th>Dia</th>
			<th>Mes</th>
			<th>A&ntilde;o</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $row_rsRecibo["dia"]?></td>
			<td><?php echo $row_rsRecibo["mes"]?></td>
			<td><?php echo $row_rsRecibo["anio"]?></td>
		</tr>
	</tbody>
</table>
<div id="monto">
<p><strong>U$</strong> <span class="under"><?php echo $row_rsRecibo["montod"]?></span></p>
<p><strong>C$</strong> <span class="under"><?php echo $row_rsRecibo["montoc"]?></span></p>
</div>
<p>Recibi de: <span><?php echo $row_rsRecibo["cliente"]?></span></p>
<p>La cantidad de: <span><?php echo num2letras($row_rsRecibo["total"]) ?> dolares</span></p>
<p>En concepto de:</p>
<ul>
<?php while($row_rsDetails = $rsDetails->fetch_array(MYSQLI_ASSOC)){ ?>
<li><?php echo $row_rsDetails["concepto"]?></li>
<?php }?>
</ul>
</div>
</body>
</html>
