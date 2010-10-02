<?php
/**
* class UserFromPasswd
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
$iddoc = (int)$_GET["doc"];
if(!$iddoc){
    die();
}else{
    $query = "
             SELECT
            d.iddocumento,
            d.ndocimpreso,
            DATE_FORMAT(d.fechacreacion,'%d/%m/%Y') as fechacreacion,
            l.procedencia,
             SUM(a.unidades) as unidadestotal,
            SUM(a.fob) AS fobtotal,
            SUM(a.flete) AS fletetotal,
            SUM(a.seguro) AS segurototal,
            SUM(a.otrosgastos) AS otrosgastostotal,
            SUM(a.cif) AS ciftotal,
            SUM(a.impuestos) as impuestototal,
            SUM(a.comision) as comisiontotal,
            SUM(a.agencia) as agenciatotal,
            SUM(a.almacen) as almacentotal,
            SUM(a.papeleria) as papeleriatotal,
            SUM(a.transporte) as transportetotal,
        SUM(costototal) as totalcosto,
        SUM(costounit) AS costounittotal,
        SUM(costounit * tc.tasa) as costounitcordobatotal,
      --  SUM(costototal * tc.tasa) as totalcostocordoba,
            a.iddocumento,
            b.nombrebodega,
            p.nombre,
            tc.tasa
        FROM vw_articulosprorrateados a
    JOIN documentos d on d.iddocumento=a.iddocumento
  JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
  JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["PROVEEDOR"]}
  JOIN bodegas b ON b.idbodega = d.idbodega
  JOIN liquidaciones l ON l.iddocumento = d.iddocumento
    JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
    WHERE d.iddocumento='$iddoc'
  GROUP BY l.iddocumento
 ;
    ";
	$rsDocumento = $dbc->query($query);
	$row_rsDocumento = $rsDocumento->fetch_array(MYSQLI_ASSOC);

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
            costototal as totalcostod,
            costounit as costounitd,
          --  costototal * tc.tasa as totalcostoc,
            costounit * tc.tasa as costounitc,
        v.iddocumento
        FROM vw_articulosprorrateados v
        JOIN vw_articulosdescritos a ON a.idarticulo = v.idarticulo
	JOIN documentos d on d.iddocumento=v.iddocumento
	JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
	WHERE d.iddocumento= '$iddoc'
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
<title>Llantera Esquipulas: Liquidaci&oacute;n <?php echo utf8tohtml($row_rsDocumento["ndocimpreso"]) ?></title>
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
<p><strong>Poliza: </strong><?php echo utf8tohtml($row_rsDocumento["ndocimpreso"]) ?></p>
<p><strong>Procedencia: </strong><?php echo utf8tohtml($row_rsDocumento["procedencia"]) ?></p>
</div>
<div class="float">
<p><strong>Fecha: </strong><?php echo $row_rsDocumento["fechacreacion"] ?></p>
<p><strong>Bodega: </strong><?php echo utf8tohtml($row_rsDocumento["nombrebodega"]) ?></p>
</div>
<div class="float">
<p><strong>Tipo Cambio: </strong><?php echo $row_rsDocumento["tasa"] ?></p>
<p><strong>Proveedor: </strong><?php echo utf8tohtml($row_rsDocumento["nombre"]) ?></p>

</div>
</div>
<table border="1pt" frame="box" rules="rows" cellpadding="2" id="details"
	cellspacing="0" summary="Reporte de liquidacion">
	<tr>
		<th style="width:150pt">Articulo</th>
		<th >Cantidad</th>
		<th >Precio Unit</th>
		<th >FOB</th>
		<th >Flete</th>
		<th >Seguro</th>
		<th >Otros Gastos</th>
		<th >CIF</th>
		<th >Comisi&oacute;n</th>
		<th >Agencia</th>
		<th >Almacen</th>
		<th >Papeleria</th>
		<th >Impuestos</th>
		<th >Acarreo</th>
		<th >Total Costo US$</th>
		<th >Costo US$</th>
		
		<th >Costo C$</th>
	</tr>
        <?php
        $color = 1;
        while ( $row_rsArticulo = $rsArticulos->fetch_assoc() ){
            $color++;
        ?>

        <tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td style="text-align:left"><?php echo $row_rsArticulo["descripcion"] ?></td>
		<td><?php  echo $row_rsArticulo["unidades"] ?></td>
		<td><?php echo $row_rsArticulo["costocompra"]  != 0 ? number_format($row_rsArticulo["costocompra"], 4) : 0 ; ?></td>
		<td><?php  echo $row_rsArticulo["fob"] != 0 ? number_format($row_rsArticulo["fob"],4) : 0; ?></td>
		<td><?php  echo $row_rsArticulo["flete"] != 0 ? number_format($row_rsArticulo["flete"],4) : 0 ?></td>
		<td><?php  echo $row_rsArticulo["seguro"] != 0 ? number_format($row_rsArticulo["seguro"],4) : 0 ?></td>
		<td><?php  echo $row_rsArticulo["otrosgastos"] != 0 ? number_format($row_rsArticulo["otrosgastos"],4)  :0 ?></td>
		<td><?php  echo $row_rsArticulo["cif"] != 0 ? number_format($row_rsArticulo["cif"],4) :0 ?></td>
		<td><?php  echo $row_rsArticulo["comision"] != 0 ? number_format($row_rsArticulo["comision"],4) :0 ?></td>
		<td><?php  echo $row_rsArticulo["agencia"] != 0 ? number_format($row_rsArticulo["agencia"],4) :0 ?></td>
		<td><?php  echo $row_rsArticulo["almacen"] != 0 ? number_format($row_rsArticulo["almacen"], 4) :0 ?></td>
		<td><?php  echo $row_rsArticulo["papeleria"] != 0 ? number_format( $row_rsArticulo["papeleria"] ,4    ):0 ?></td>
		<td><?php  echo $row_rsArticulo["impuestos"] != 0 ? number_format($row_rsArticulo["impuestos"], 4) :0 ?></td>
		<td><?php  echo $row_rsArticulo["transporte"] != 0 ? number_format( $row_rsArticulo["transporte"] ,4    ) :0 ?></td>
		<td><?php  echo $row_rsArticulo["totalcostod"] != 0 ? number_format( $row_rsArticulo["totalcostod"] ,4    ) :0 ?></td>
		<td><?php  echo $row_rsArticulo["costounitd"] != 0 ? number_format( $row_rsArticulo["costounitd"] ,4    ) :0 ?></td>
	
		<td><?php  echo $row_rsArticulo["costounitc"] != 0 ? number_format( $row_rsArticulo["costounitc"] ,4) :0 ?></td>
	</tr>

	<?php } $color ++; ?>
	      
<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
    

    <td><strong>Totales</strong></td>
    <td><?php echo $row_rsDocumento["unidadestotal"] ?></td>
    <td></td>
    <td><?php echo $row_rsDocumento["fobtotal"] !=0 ? number_format( $row_rsDocumento["fobtotal"],4) : 0; ?></td>
    <td><?php echo $row_rsDocumento["fletetotal"] != 0 ? number_format( $row_rsDocumento["fletetotal"],4) : 0; ?></td>
    <td><?php echo $row_rsDocumento["segurototal"] != 0 ? number_format( $row_rsDocumento["segurototal"],4) : 0; ?></td>
    <td><?php echo $row_rsDocumento["otrosgastostotal"] != 0 ? number_format( $row_rsDocumento["otrosgastostotal"],4): 0;  ?></td>
    <td><?php echo $row_rsDocumento["ciftotal"] != 0 ? number_format( $row_rsDocumento["ciftotal"],4): 0;  ?></td>
    <td><?php echo $row_rsDocumento["comisiontotal"] != 0 ? number_format( $row_rsDocumento["comisiontotal"],4): 0;  ?></td>
     <td><?php echo $row_rsDocumento["agenciatotal"] != 0 ? number_format( $row_rsDocumento["agenciatotal"],4): 0;  ?></td>
    <td><?php echo $row_rsDocumento["almacentotal"] != 0 ? number_format( $row_rsDocumento["almacentotal"],4): 0;  ?></td>
    
    <td><?php echo $row_rsDocumento["papeleriatotal"] != 0 ? number_format( $row_rsDocumento["papeleriatotal"],4): 0;  ?></td>
    <td><?php echo $row_rsDocumento["impuestototal"] != 0 ? number_format( $row_rsDocumento["impuestototal"],4): 0;  ?></td>
    <td><?php echo $row_rsDocumento["transportetotal"] != 0 ? number_format( $row_rsDocumento["transportetotal"],4): 0;  ?></td>

    <td><?php echo $row_rsDocumento["totalcosto"] != 0 ? number_format( $row_rsDocumento["totalcosto"],4): 0;  ?></td>
    <td><?php echo $row_rsDocumento["costounittotal"] != 0 ? number_format( $row_rsDocumento["costounittotal"],4): 0;  ?></td>
    <td><?php echo $row_rsDocumento["costounitcordobatotal"] != 0 ? number_format( $row_rsDocumento["costounitcordobatotal"],4): 0;  ?></td>
 
    
</tr>

</table>    
    

<?php if($row_rsDocumento["observacion"]){ ?>
<h2>Observaciones</h2>
<div><?php echo $row_rsDocumento["observacion"] ?></div>
	<?php } ?>

</body>
</html>