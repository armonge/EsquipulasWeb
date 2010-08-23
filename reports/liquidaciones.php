<?php
require_once "../functions.php";
$iddoc = $_GET["doc"];
if(!$iddoc){
 
    die();
}else{
    $query = "
        SELECT
            d.iddocumento,
            d.ndocimpreso,
            DATE_FORMAT(d.fechacreacion,'%d/%m/%Y') as fechacreacion,
            l.procedencia,
	    l.unidadestotal,	    
	    l.fobtotal,
	    l.fletetotal,
            l.segurototal,
            l.otrosgastostotal,
	    l.ciftotal,
            l.papeleriatotal,
            l.transportetotal,
	    l.comisiontotal,
	    l.agenciatotal,
            l.almacentotal,
            l.impuestototal,
            l.pesototal,
            p.nombre,
            b.nombrebodega,
            valorcosto as iso,
	    tc.tasa,
	     -- ROUND(l.ciftotal + l.papeleriatotal +  l.transportetotal + l.comisiontotal + l.agenciatotal+ l.almacentotal + l.impuestototal,4) as totalcosto,
	    d.total as totalcosto,
	    d.total / l.unidadestotal as costounittotal,
	    (d.total / l.unidadestotal ) * tc.tasa as costounitcordobatotal
	   -- ROUND((l.ciftotal + l.papeleriatotal +  l.transportetotal + l.comisiontotal + l.agenciatotal+ l.almacentotal + l.impuestototal)/l.unidadestotal,4) as costounittotal,
           -- ROUND(((l.ciftotal + l.papeleriatotal +  l.transportetotal + l.comisiontotal + l.agenciatotal+ l.almacentotal + l.impuestototal)/l.unidadestotal) * tc.tasa,4) as costounitcordobatotal
        FROM documentos d
        JOIN vw_liquidacioncontotales l ON d.iddocumento = l.iddocumento
        JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
        JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["PROVEEDOR"]}
        JOIN bodegas b ON b.idbodega = d.idbodega
        LEFT JOIN costosxdocumento cxd ON d.iddocumento = cxd.iddocumento
        LEFT JOIN costosagregados ca ON cxd.idcostoagregado = ca.idcostoagregado
        JOIN tiposcambio tc ON d.idtipocambio = tc.idtc
        WHERE ca.idtipocosto = 6 AND d.ndocimpreso = '$iddoc'
        GROUP BY d.iddocumento
    ";
	$rsDocumento = $dbc->query($query);
	$row_rsDocumento = $rsDocumento->fetch_assoc();

	$rsArticulos = $dbc->query("
	         SELECT
            a.idarticulo,
            descripcion,
            unidades,
            costocompra,
            fob,
            flete,
            seguro,
            otrosgastos,
            cif,
            impuestos,
            comision,
            agencia,
            almacen,
            papeleria,
            transporte,
	    costototal as totalcosto,
	    costounit,
	    ROUND(costototal * tc.tasa,4) as totalcostocordoba,
	    ROUND(costounit * tc.tasa,4) as costounitcordoba,
            v.iddocumento
        FROM vw_articulosprorrateados v
        JOIN vw_articulosdescritos a ON a.idarticulo = v.idarticulo
	JOIN documentos d on d.iddocumento=v.iddocumento
	JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
	WHERE d.ndocimpreso= '$iddoc'
	");



}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Llantera Esquipulas: Liquidaci&oacute;n <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<style type="text/css">
html{
    border:0;
    padding:0;
    margin:0;
}
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11pt;
	width:1170pt;
/* 	border:1px solid #000; */
}
.gray {
	background: #f4f4f4;
}

.rigth {
	text-align: right;
}

h2 {
	border-bottom: 4px solid #000000;
	font-size: 24pt;
	text-align: center;
}

.noborder {
	border: 0;
}

th {
	background: #4f4f4f;
	color: #fff;
}
h1 {
      font-size: 24pt;
      text-align: center;
}
.float {
	float: left;
	width: 33%
}

p {
	margin: 10px;
	font-size: 13pt;
}


thead {
	display: table-header-group;
}

tbody {
	display: table-row-group;
}
table{
    text-align:center;
    padding:1%;
    margin:1%;
}
#details{
    table-layout:fixed;
    width:98%;
    
    
}
@media print {
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

}
</style>
</head>
<body>
<h1>Llantera Esquipulas</h1>
<div style="width:100%">
<h2>Liquidaci&oacute;n de costos</h2>
<div class="float">

<p><strong>Poliza: </strong><?php echo $row_rsDocumento["ndocimpreso"] ?></p>
<p><strong>Procedencia: </strong><?php echo $row_rsDocumento["procedencia"] ?></p>
</div>
<div class="float">
<p><strong>Fecha: </strong><?php echo $row_rsDocumento["fechacreacion"] ?></p>
<p><strong>Bodega: </strong><?php echo $row_rsDocumento["nombrebodega"] ?></p>
</div>
<div class="float">
<p><strong>Tipo Cambio: </strong><?php echo $row_rsDocumento["tasa"] ?></p>
<p><strong>Proveedor: </strong><?php echo $row_rsDocumento["nombre"] ?></p>

</div>
</div>
<table border="1pt" frame="box" rules="rows" cellpadding="2" id="details"
	cellspacing="0" summary="Reporte de liquidacion">
	<tr>
		<th  style="width:70pt">Articulo</th>
		<th style="width:25pt">Cantidad</th>
		<th style="width:25pt">Precio Unit</th>
		<th style="width:30pt">FOB</th>
		<th style="width:30pt">Flete</th>
		<th style="width:30pt">Seguro</th>
		<th style="width:30pt">Otros Gastos</th>
		<th style="width:30pt">CIF</th>
		<th style="width:25pt">Comisi&oacute;n</th>
		<th style="width:20pt">Agencia</th>
		<th style="width:20pt">Almacen</th>
		<th style="width:25pt">Papeleria</th>
		<th style="width:30pt">Impuestos</th>
		<th style="width:25pt">Acarreo</th>
		<th style="width:30pt">Total Costo US$</th>
		<th style="width:30pt">Costo Dolares</th>
		<th style="width:30pt">Costo Cordobas</th>
	</tr>
	<?php $color = 1; 	while ( $row_rsArticulo = $rsArticulos->fetch_assoc() ){	 ?>
	<?php$impuestostotal= int(0)
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td style="text-align:left"><?php echo $row_rsArticulo["descripcion"] ?></td>
		<td><?php echo $row_rsArticulo["unidades"] ?>
		<td><?php echo $row_rsArticulo["costocompra"]  != 0 ? number_format($row_rsArticulo["costocompra"], 4) : 0 ; ?></td>
		<td><?php echo $row_rsArticulo["fob"] != 0 ? number_format($row_rsArticulo["fob"],4) : 0; ?></td>

		<td><?php echo $row_rsArticulo["flete"] != 0 ? number_format($row_rsArticulo["flete"],4) : 0 ?></td>
		<td><?php echo $row_rsArticulo["seguro"] != 0 ? number_format($row_rsArticulo["seguro"],4) : 0 ?></td>
		<td><?php echo $row_rsArticulo["otrosgastos"] != 0 ? number_format($row_rsArticulo["otrosgastos"],4)  :0 ?></td>
		<td><?php echo  $row_rsArticulo["cif"] != 0 ? number_format($row_rsArticulo["cif"],4) :0 ?></td>
		<td><?php echo  $row_rsArticulo["comision"] != 0 ? number_format($row_rsArticulo["comision"],4) :0 ?></td>
		<td><?php echo  $row_rsArticulo["agencia"] != 0 ? number_format($row_rsArticulo["agencia"],4) :0 ?></td>
		<td><?php echo  $row_rsArticulo["almacen"] != 0 ? number_format($row_rsArticulo["almacen"], 4) :0 ?></td>
		<td><?php echo  $row_rsArticulo["papeleria"] != 0 ? number_format( $row_rsArticulo["papeleria"] ,4    ):0 ?></td>
		<td><?php echo  $row_rsArticulo["impuestos"] != 0 ? number_format($row_rsArticulo["impuestos"], 4) :0 ?></td>
		<td><?php echo  $row_rsArticulo["transporte"] != 0 ? number_format( $row_rsArticulo["transporte"] ,4    ) :0 ?></td>
		<td><?php echo  $row_rsArticulo["totalcosto"] != 0 ? number_format( $row_rsArticulo["totalcosto"] ,4    ) :0 ?></td>
		<td><?php echo  $row_rsArticulo["costounit"] != 0 ? number_format( $row_rsArticulo["costounit"] ,4    ) :0 ?></td>
		<td><?php echo  $row_rsArticulo["costounitcordoba"] != 0 ? number_format( $row_rsArticulo["costounitcordoba"] ,4) :0 ?></td>
	</tr>

	<?php }?>
	      
<tr>
    
    <td><strong>Totales</td>
    <td><?php echo $row_rsDocumento["unidadestotal"] ?></li>    
    <td></td>
    <td><?php echo number_format( $row_rsDocumento["fobtotal"],4) ?></li>    
    <td><?php echo number_format( $row_rsDocumento["fletetotal"],4) ?></li>
    <td><?php echo number_format( $row_rsDocumento["segurototal"],4) ?></li>
    <td><?php echo number_format( $row_rsDocumento["otrosgastostotal"],4) ?></li>
    <td><?php echo number_format( $row_rsDocumento["ciftotal"],4) ?></li>
    <td><?php echo number_format( $row_rsDocumento["comisiontotal"],4) ?></li>
     <td><?php echo number_format( $row_rsDocumento["agenciatotal"],4) ?></li>
    <td><?php echo number_format( $row_rsDocumento["almacentotal"],4) ?></li>
    
    <td><strong></strong><?php echo number_format( $row_rsDocumento["papeleriatotal"],4) ?></li>
    <td><strong></strong><?php echo number_format( $row_rsDocumento["impuestototal"],4) ?></li>
    <td><strong></strong><?php echo number_format( $row_rsDocumento["transportetotal"],4) ?></li>

    <td><strong></strong><?php echo number_format( $row_rsDocumento["totalcosto"],4) ?></li>
    <td><strong></strong><?php echo number_format( $row_rsDocumento["costounittotal"],4) ?></li>
    <td><strong></strong><?php echo number_format( $row_rsDocumento["costounitcordobatotal"],4) ?></li>
    
</tr>

</table>    
    

<?php if($row_rsDocumento["observacion"]){ ?>
<h2>Observaciones</h2>
<div><?php echo $row_rsDocumento["observacion"] ?></div>
	<?php } ?>

</body>
</html>