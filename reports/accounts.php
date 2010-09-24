<?php
require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("contabilidadrep") || $_SESSION["user"]->hasRole("gerencia"))){
    die("Usted no tiene permisos para ver reportes");
}
$selection = $_GET["ac"];
if($selection){
$data = array_unique(explode(",",$selection));
$return = array();
$startDate = $dbc->real_escape_string($_GET["startDate"]);
$endDate = $dbc->real_escape_string($_GET["endDate"]);
foreach($data as $d){
	if((int)$d){
		$query= ("
			SELECT cc.idcuenta, UNIX_TIMESTAMP(DATE(fechacreacion))*1000 as stamp, SUM(monto) as total, descripcion as nombre
			FROM cuentasxdocumento cxd
			JOIN documentos d ON d.iddocumento = cxd.iddocumento
			JOIN cuentascontables cc ON cxd.idcuenta = cc.idcuenta
			WHERE cc.idcuenta = $d ".
			 ( $startDate ? " AND DATE(fechacreacion) >= '" . $startDate ."'": "" )
			 .( $endDate ? " AND DATE(fechacreacion) <= '" . $endDate . "'": "" )
			." GROUP BY DATE(fechacreacion)
			ORDER BY DATE(fechacreacion)
		");
		$data = $dbc->query($query);
		$inner = array();

		if($data->num_rows){
			while ($row = $data->fetch_array(MYSQLI_ASSOC)) {
				$label = $row["nombre"];
				$inner[] = array($row["stamp"],$row["total"]);
			}
		$return[] = array("label"=>$label,"data"=>$inner);
		}
	}
}
echo json_encode($return);
	die();
}
$rsAccounts = $dbc->query("SELECT idcuenta, codigo, descripcion FROM cuentascontables WHERE padre IS NOT NULL AND codigo != '' ");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<meta http-equiv="Content-Type"	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas: Historico de Cuentas</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />

<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.flot.js"></script>

<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-es.js"></script>

<!-- Descargar la imagen -->
<script type="text/javascript" src="js/jq.flot.text.js"></script>
<script type="text/javascript" src="js/base64.js"></script>
<script type="text/javascript" src="js/canvas2image.js"></script>

<script type="text/javascript">
var jsonaddress = "<?php echo $basedir ?>reports/accounts.php";
var moneysimbol = "C$";
$(function(){
    $("input[type='radio']").click(function(){
	if($(this).val()==1){
	    $(".code").hide();
	    $(".description").show();
	}else{
	    $(".code").show();
	    $(".description").hide();
	}
    });
    $.getScript("js/linegraph.js");
    $("#download-image").click(function(){
        var canvas = document.getElementsByTagName("canvas")[0]
        if(canvas.getContext){
//             var context = canvas.getContext("2d");
//             context.fillStyle = "green";r
//             context.fillRect(50, 50, 100, 100);
//             // no argument defaults to image/png; image/jpeg, etc also work on some
//             // implementations -- image/png is the only one that must be supported per spec.
//             window.location = canvas.toDataURL("image/png");
                Canvas2Image.saveAsPNG(canvas);
        }else{
            alert("Se necesita otro navegador para esta función")
        }
          return false;
    });
});
</script>
<style type="text/css">
#m2 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m2 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
#left-column{
	width:auto;
}
.code{
    display:none;
}
</style>

</head>
<body>
<div id="wrap">
 <?php include "../header.php"?>
<div id="content">
<div id="left-column">
<div class="ui-widget"><label for="from">Desde: </label> <input
	type="text" id="from" name="from" /> <label for="to">hasta: </label> <input
	type="text" id="to" name="to" />
	<label>Descripci&oacute;n<input type="radio" value="1"  name="view" checked="checked"/></label>
    <label>Codigo: <input type="radio" value="2"  name="view" /></label>
    <a href="#" id="download-image">Descargar imagen</a>
</div>

<div id="leftcol">
<ul>
<?php while( $row_rsAccount= $rsAccounts->fetch_assoc()){ ?>
	<li>
	    <label>
		 <input name="selected[]" type="checkbox" value="<?php echo $row_rsAccount["idcuenta"] ?>" />
		 <span class="description"><?php echo utf8tohtml($row_rsAccount["descripcion"], TRUE) ?></span>
		 <span class="code"><?php echo utf8tohtml($row_rsAccount["codigo"], TRUE) ?></span>
	    </label>
	 </li>
<?php } ?>
</ul>
</div>
<div id="canvas"></div>
</div>
<?php include "../footer.php" ?></div>
</div>
</body>
</html>

