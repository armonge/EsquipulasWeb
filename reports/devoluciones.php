<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";

$iddoc = (int)$_GET["doc"];

if(!$iddoc){
    die();
}
$query = "
SELECT
    d.ndocimpreso,
    padre.ndocimpreso as factura,
    (d.total ) as total,
    p.nombre as cliente,
    d.fechacreacion,
    d.observacion,
    d.idestado
FROM documentos d
JOIN docpadrehijos dpd ON dpd.idhijo = d.iddocumento
JOIN documentos padre ON dpd.idpadre = padre.iddocumento
JOIN personasxdocumento pxd ON d.iddocumento = pxd.iddocumento
JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona != {$persontypes["USUARIO"]}
LEFT JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
WHERE d.idtipoDoc = {$docids["NOTACREDITO"]}
AND d.iddocumento = $iddoc
";
$rsDocumento = $dbc->query($query);
$row_rsDocumento = $rsDocumento->fetch_assoc();

$rsArticulos = $dbc->query("
SELECT
ad.descripcion as descripcion,
axd.unidades,
(axd.precioventa ) as costounit
FROM articulosxdocumento axd
JOIN vw_articulosdescritos ad ON axd.idarticulo = ad.idarticulo
JOIN documentos d ON d.iddocumento = axd.iddocumento
JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
WHERE axd.iddocumento = $iddoc
");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"	content="application/xhtml+xml; charset=UTF-8" />
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Llantera Esquipulas: Devoluci&oacute;n No <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<link rel="stylesheet" type="text/css" href="css/flick/jq.ui.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript">
$(function(){
    $('#accept, #deny').button()
});
</script>
<style type="text/css">
.gray {
    background: #f4f4f4;
}

.rigth {
    text-align: right;
}

h2 {
    border-bottom: 4px solid #000000;
    clear:both;
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
    width: 33%
}

p {
    margin: 10px;
}

table {
    text-align: center;
    width:100%;
}
thead {
        display:table-header-group;
        border-width: 1px 0;
        border-color:#000;
        border-style:solid;
}
tbody {
    display:table-row-group;
}
#action{
    clear:both;
    text-align:center;
    float:left;
    width:100%;
    margin:10px;
}
.state{
    text-align:center;
    border:0;
}
@media print{
    html{
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10pt;
        margin:0;
        padding:0;
        color:#000;
    }
    body{
        width:792pt;
    }


    .gray {
        background: #fff;
    }
    th {
        background: #fff;
        color:#000;
    }
    thead{
        color:#000;
    }
    #footer, #logo, #uname, #menu, .ui-widget, #action, .state{
        display:none;
    }
} 
</style>
</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
<?php if($_SESSION["user"]->hasRole("gerencia")   && $row_rsDocumento["idestado"] != $docstates["CONFIRMADO"]){ ?>
    <h2 class="state"><?php if($row_rsDocumento["idestado"] == $docstates["PENDIENTE"]){ echo  "Devoluci&oacute;n pendiente de autorizaci&oacute;n"; } ?> </h2>
<?php } ?>

<h2>Devoluci&oacute;n</h2>
<p><strong>Numero de Devolucion:</strong> <?php echo $row_rsDocumento["ndocimpreso"] ?>
<br />
Factura: <?php echo $row_rsDocumento["factura"] ?><br />
Fecha del documento: <?php echo $row_rsDocumento["fechacreacion"] ?> <br />
Cliente: <?php echo $row_rsDocumento["cliente"] ?></p>
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1"	summary="Reporte de partida contable">
<thead>
	<tr>
		<th>Articulo</th>
		<th>Unidades</th>
		<th>Precio de venta</th>
		<th>Total</th>
	</tr>
	</thead>
	<tbody>
	<?php $color = 1; while ($row_rsArticulos = $rsArticulos->fetch_assoc() ){ $color++;  ?>
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td><?php echo $row_rsArticulos["descripcion"] ?></td>
		<td><?php echo $row_rsArticulos["unidades"] ?></td>
		<td>US$ <?php echo number_format($row_rsArticulos["costounit"],4) ?></td>
		<td>US$ <?php echo number_format($row_rsArticulos["unidades"]*$row_rsArticulos["costounit"],4) ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2"></td>
		<td>TOTAL</td>
		<td>US$ <?php echo number_format($row_rsDocumento["total"],4) ?></td>
	</tr>
	</tbody>
</table>
<?php if($row_rsDocumento["observacion"]){ ?>
<h2>Observaciones</h2>
<div><?php echo $row_rsDocumento["observacion"] ?></div>
<?php } ?>

<?php
if($_SESSION["user"]->hasRole("gerencia")   && $row_rsDocumento["idestado"] != $docstates["CONFIRMADO"]){
    if($row_rsDocumento["idestado"] == $docstates["PENDIENTE"]){ ?>
<div id="action">
        <a id="accept" href="<?php echo $base ?>administration/authorizations.php?devdoc=<?php echo $iddoc ?>"
         onclick="return confirm('¿Realmente desea confirmar esta devoluci&oacute;n?')" >Autorizar</a>
        <a id="deny" href="<?php echo $base ?>administration/authorizations.php?devdel=<?php echo $iddoc ?>"
        onclick="return confirm('¿Realmente desea denegar esta devoluci&oacute;n?')">Denegar</a>
</div>
<?php } }?>
<?php include "../footer.php" ?>
</div>

</div>
</body>
</html>
