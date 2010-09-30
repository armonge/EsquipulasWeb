<?php
require_once "../functions.php";
$id = (int)$_GET["id"];
if(!$id){
    die();
}
$query = "
SELECT
    p.idpersona,
    p.nombre,
    p.telefono,
    p.email,
    p.ruc,
    p.activo,
    SUM(d.total) as total
FROM personas p
JOIN personasxdocumento pxd ON pxd.idpersona = p.idpersona
JOIN documentos d ON d.iddocumento = pxd.iddocumento AND d.idtipodoc = {$docids["FACTURA"]}
WHERE p.idpersona = $id AND p.tipopersona = {$persontypes["CLIENTE"]}
";
$rsDetails = $dbc->query($query);
$row_rsDetails = $rsDetails->fetch_array(MYSQLI_ASSOC);

$query="
SELECT
    d.iddocumento,
    d.ndocimpreso,
    UNIX_TIMESTAMP(d.fechacreacion)*1000 as stamp,
    d.total
FROM documentos d
JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
WHERE pxd.idpersona = $id AND d.idtipodoc = {$docids["FACTURA"]}
ORDER BY DATE(d.fechacreacion)
";
$rsTransactions = $dbc->query($query);
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
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.corner.js"></script>
<script type="text/javascript" src="js/jq.flot.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
#m5 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m5 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
#content span{
    width:20%;
    display:inline-block;
}
h2:hover{
    color:blue;
    cursor:pointer;
}
#canvas{
    width:90%;
    height:300px;
}
</style>
<?php if($rsTransactions->num_rows){ ?>
<script type="text/javascript">
$(function(){
    $("h2").click(function(){
	$(this).next().toggle();
    });
    Number.prototype.moneyfmt = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
    var moneysimbol = "US$";
    var mmToMonth = new Array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
    function showLocalDate(timestamp)
    {
      var dt = new Date(timestamp);
      var mm = mmToMonth[dt.getMonth()];
      return dt.getDate()+ "-"+ mm+ "-" +dt.getFullYear();
    }
    var placeholder = $("#canvas");
    
    var options = {
	    lines:{show:true},
	    points:{show:true},
	    xaxis: {
            mode:'time',
            timeformat: "%d-%m-%y %h:%M %P",
            ticks:5,
            autoscaleMargin:0.1
		},
	    yaxis: {
		    tickFormatter: function(val, axis){
				return moneysimbol+val.moneyfmt(0);
			},
			 autoscaleMargin:0.1
		},
		tickSize:1,
	   	minTickSize: [1, "day"],
	   	grid: {
		    hoverable: true,
		    clickable:true
		}

    };
    var legend = {
		    show: true,
		    position: "ne",
		    backgroundOpacity: 0
    };
    data = [{"label":"Ultimas Compras","data":[
    <?php while($row_rsTransaction = $rsTransactions->fetch_assoc()){
	echo "[".$row_rsTransaction["stamp"].",".$row_rsTransaction["total"].",".$row_rsTransaction["iddocumento"]."],";
    }
    ?>]}];
    function showTooltip(x, y, contents) {
	/*<![CDATA[*/
	$('<div id="tooltip">' + contents + '<\/div>').css( {
	    /*]]>*/
	    position: 'absolute',
	    display: 'none',
	    top: y + 5,
	    left: x + 5,
	    border: '1px solid #fdd',
	    padding: '2px',
	    'background-color': '#f4f4f4',
	    opacity: 0.80
	}).appendTo("body").fadeIn(200);
    }
    $.plot(placeholder, data, options)
    placeholder.bind("plothover", function (event, pos, item) {
	$("#x").text(pos.x.toFixed(2));
	$("#y").text(pos.y.toFixed(2));
	    if (item) {
		if (previousPoint != item.datapoint) {
		    previousPoint = item.datapoint;

		    $("#tooltip").remove();
		    var x = item.datapoint[0].toFixed(2),
			y = item.datapoint[1].toFixed(2);

		    showTooltip(item.pageX, item.pageY,
			moneysimbol+" "+item.datapoint[1].moneyfmt(4,'.',',') + " / " + showLocalDate(item.datapoint[0])
		     );

		}
	    }
	    else {
		$("#tooltip").remove();
		previousPoint = null;
	    }
    });
    placeholder.bind("plotclick", function(event, pos, item){
	window.location = "<?php echo $basedir ?>reports/facturas.php?doc="+item.series.data[item.dataIndex][2];
    });
});
</script >
<?php } ?>
</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
    <h1>Clientes: <?php echo $row_rsDetails["nombre"] ?></h1>
	<p>
		<a href="clients/edit.php?id=<?php echo $id ?>">Editar</a> /
		<?php if($row_rsDetails["activo"] == 1){ ?>
		<a href="clients/?del=<?php echo $id ?>" onclick="return confirm('Realmente desea establecer este cliente como inactivo?')" title="Esto solo borrar al cliente de la lista de clientes pero no eliminara los registros de sus transacciones">Borrar cliente</a>
		<?php }else{ ?>
		<a href="clients/?del=<?php echo $id ?>" onclick="return confirm('Realmente desea establecer este cliente como activo?')" >Activar cliente</a>
		<?php } ?>
    </p>
    <p>
	<span><strong>Telef&oacute;no:</strong> <?php echo $row_rsDetails["telefono"] ?></span>
	<span><strong>e-mail:</strong> <?php echo $row_rsDetails["email"] ?></span>
	<span><strong>RUC/Id:</strong> <?php echo $row_rsDetails["ruc"] ?></span>
	<span><strong>Total de Compras:</strong> US$<?php echo number_format($row_rsDetails["total"],4) ?></span>
    </p>
    <?php if($rsTransactions->num_rows){ ?>
	<h2>Ultimas Compras</h2>
	<div id="canvas">
	</div>
    <?php } ?>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
