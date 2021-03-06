<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("ventasrep") || $_SESSION["user"]->hasRole("gerencia"))){
    die("Usted no tiene permisos para ver reportes");
}
$iddoc = (int)$_GET["doc"];
if(!$iddoc){
	die();
}
$dbc->query("SET lc_time_names = 'es_NI';");
$query = "
SELECT
	d.iddocumento, 
	d.ndocimpreso AS 'Numero de Arqueo', 
	UPPER(DATE_FORMAT(fechacreacion,'%d DE %M DEL  %Y')) AS 'fecha', 
	p.nombre AS 'Arqueador', 
	CONCAT('US$',FORMAT(d.total,4))  AS 'totald',
	tc.tasa AS 'cambio'
FROM documentos d  
JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["USUARIO"]}
WHERE d.idtipodoc = {$docids["ARQUEO"]}
AND d.iddocumento = $iddoc
";
$rsDocumento = $dbc->query($query);

$row_rsDocumento = $rsDocumento->fetch_array(MYSQLI_ASSOC);

$query = "
SELECT 
l.cantidad ,
CONCAT_WS(' ', tm.simbolo, FORMAT(de.valor,4)) as 'denominacion',
CONCAT_WS(' ', tm.simbolo,FORMAT(l.cantidad * de.valor,4) ) as 'total',
l.cantidad * de.valor as 'totalnf'
FROM lineasarqueo l
JOIN denominaciones de ON de.iddenominacion = l.iddenominacion
JOIN tiposmoneda tm ON de.idtipomoneda = tm.idtipomoneda
JOIN documentos d ON d.iddocumento = l.iddocumento
JOIN tiposcambio tc ON d.idtipocambio = tc.idtc
WHERE tm.idtipomoneda = {$moneda["CORDOBA"]}
AND d.iddocumento = $iddoc
";
$rsCordobas = $dbc->query($query);

$query = "
SELECT 
l.cantidad ,
CONCAT_WS(' ', tm.simbolo, FORMAT(de.valor,4)) as 'denominacion',
CONCAT_WS(' ', tm.simbolo,FORMAT(l.cantidad * de.valor,4) ) as 'total',
l.cantidad * de.valor as 'totalnf'
FROM lineasarqueo l
JOIN denominaciones de ON de.iddenominacion = l.iddenominacion
JOIN tiposmoneda tm ON de.idtipomoneda = tm.idtipomoneda
JOIN documentos d ON d.iddocumento = l.iddocumento
JOIN tiposcambio tc ON d.idtipocambio = tc.idtc
WHERE tm.idtipomoneda = {$moneda["DOLAR"]}
AND d.iddocumento = $iddoc
";
$rsDolares = $dbc->query($query);

?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<link rel="shortcut icon" href="&lt;?php echo $basedir ?&gt;favicon.ico" />
<title>Llantera Esquipulas: Cheque <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<style type="text/css">
y 
html {
	border: 0;
	padding: 0;
	margin: 0;
}

body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	float: left;
	width: 750pt;
	padding: 0;
	margin: 0;
	/* 	border:1px solid #000; */
}

table{
	text-align:center;
	table-layout:fixed;
}
.gray {
	background: #f4f4f4;
}

.rigth {
	text-align: right;
}

h2 {
	border-bottom: 4px solid #000000;
}
.aleft{
	text-align:left;
}
.noborder {
	border: 0;
}

th {
	background: #4f4f4f;
	color: #fff;
}

.float {
	float: left;
	width: 50%
}

p {
	margin: 10px;
}

thead {
	display: table-header-group;
}

.float {
	float: left;
	width: 33%
}

tbody {
	display: table-row-group;
}

.details {
	table-layout: fixed;
	width: 50%;
	float: left;
}
#more-details{
	margin-top:30pt;
	float:left;
}
.aright {
	text-align: right;
}

@media print {
	.gray {
		background: #fff;
	}
	th {
		background: #fff;
		color: #000;
	}
	thead {
		color: #000;
	}
}
</style>
</head>
<body>
<h1>DISTRIBUIDORA DE LLANTAS ESQUIPULAS</h1>
<h2>ARQUEO Y DETALLE DE EFECTIVO DEL <?php echo $row_rsDocumento["fecha"]?></h2>
<table border="1" frame="border" rules="all" cellpadding="2"
	cellspacing="0" summary="Cordobas" class="details">
	<thead>
		<tr>
			<th colspan="3">Cordobas</th>
		</tr>
		<tr>
			<th>CANTIDAD</th>
			<th>DENOMINACI&Oacute;N</th>
			<th>TOTAL</th>
		</tr>
	</thead>
	<tbody>
	<?php $totalC = 0; while($row_rsCordoba = $rsCordobas->fetch_array(MYSQLI_ASSOC)){?>
		<tr>
			<td><?php echo $row_rsCordoba["cantidad"]?></td>
			<td><?php echo $row_rsCordoba["denominacion"]?></td>
			<td><?php $totalC = bcadd($totalC, $row_rsCordoba["totalnf"]); ; echo $row_rsCordoba["total"]?></td>
		</tr>
		<?php }?>
		<tr>
			<td></td>
			<td class="aright"><strong>TOTAL:</strong></td>
			<td><strong>C$ <?php echo number_format($totalC,4)?></strong></td>
		</tr>
	</tbody>

</table>
<table border="1" frame="border" rules="all" cellpadding="2"
	cellspacing="0" summary="Dolares" class="details">
	<thead>
		<tr>
			<th colspan="3">Dolares</th>
		</tr>
		<tr>
			<th>CANTIDAD</th>
			<th>DENOMINACI&Oacute;N</th>
			<th>TOTAL</th>
		</tr>
	</thead>
	<tbody>
	<?php $totalD = 0; while($row_rsDolar = $rsDolares->fetch_array(MYSQLI_ASSOC)){?>
		<tr>
			<td><?php echo $row_rsDolar["cantidad"]?></td>
			<td><?php echo $row_rsDolar["denominacion"]?></td>
			<td><?php $totalD = bcadd($totalD, $row_rsDolar["totalnf"]);  echo $row_rsDolar["total"]?></td>
		</tr>
		<?php }?>
		<tr>
			<td></td>
			<td class="aright"><strong>TOTAL:</strong></td>
			<td><strong>US$ <?php echo number_format($totalD,4)?></strong></td>
		</tr>
	</tbody>

</table>

<table border="1" frame="rhs" rules="none" cellpadding="2"
	cellspacing="0" summary="Otros datos" id="more-details">
	<tbody>
		<tr>
			<td class="aleft">SALDO ANTERIOR</td>
			<td style="border:1px solid #000; width:90pt;"></td>
			<td style="border:1px solid #000; width:90pt;"></td>
		</tr>
		<tr>
			<td class="aleft">TOTAL CORDOBAS SEG&Uacute;N RECIBOS DE CAJA</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">SALDO ANTERIOR + SALDO SEG&Uacute;N RECIBOS DE CAJA</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">SALDO EN US$</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">TOTAL EFECTIVO CORDOBAS (C$ + US$)</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">TOTAL FASTOS EN EFECTIVO</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">TOTAL DEPOSITOS</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">TOTAL TRANSFERENCIAS</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">CHEQUES (EMITIDOS Y RECIBIDOS)</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">BAUCHER BAC</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">GASTOS PENDIENTES DE ALMACEN</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">GASTOS PENDIENTES DE LLAEXSA</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">RECIBOS CONTRAENTREGA</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">GASTOS PENDIENTES ESQUIPULAS</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">PRESTAMO AGENCIA</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">SALDO TOTAL CAJA</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">SALDO SEG&Uacute;N ARQUEO DE CAJA</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">DIFERENCIA (SOBRANTE - FALTANTE) (CAJA)</td>
			<td style="border:1px solid #000"></td>
			<td style="border:1px solid #000"></td>
		</tr>
		<tr>
			<td class="aleft">T/CAMBIO</td>
			<td style="border:1px solid #000"><?php echo $row_rsDocumento["cambio"] ?></td>
			<td style="border:1px solid #000"><?php echo $row_rsDocumento["cambio"]*$totalD ?></td>
		</tr>
	</tbody>
</table>
</body>

</html>
