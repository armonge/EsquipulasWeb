<?php
require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("ventasrep") || $_SESSION["user"]->hasRole("gerencia"))){
    die("Usted no tiene permisos para acceder a este modulo");
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
			SELECT v.idarticulo, SUM(precioventa*unidades)*-1 as total ,UNIX_TIMESTAMP(DATE(fechacreacion))*1000 as stamp, v.descripcion as nombre
			FROM articulosxdocumento axd
			JOIN documentos d ON axd.iddocumento = d.iddocumento
			JOIN vw_articulosdescritos v ON v.idarticulo = axd.idarticulo
			WHERE axd.precioventa IS NOT NULL
			AND v.idarticulo = $d ".
				( $startDate ? " AND DATE(fechacreacion)  >= '" . $startDate ."'": "" )
				.( $endDate ? " AND  DATE(fechacreacion) <= '" . $endDate . "'": "" )
			." GROUP BY DATE(fechacreacion)
			ORDER BY fechacreacion

		");
		$data = $dbc->query($query);
		$inner = array();

		if($data->num_rows){
			while ($row = $data->fetch_array(MYSQLI_ASSOC)) {
				$label = $row["nombre"];
				$inner[] = array( (int)$row["stamp"],(int)$row["total"]);
			}
		$return[] = array("label"=>$label,"data"=>$inner);
		}
	}
}
echo json_encode($return);
	die();
}
$rsArticles= $dbc->query("SELECT idarticulo, descripcion FROM vw_articulosdescritos v LIMIT 0,1000");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas: Costos x Articulos</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.flot.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-es.js"></script>
<script type="text/javascript">
var jsonaddress = "<?php echo $basedir ?>reports/articlessales.php";
var moneysimbol = "US$";
$(function(){
    $.getScript("js/linegraph.js");
});
</script>
<style type="text/css">
#m2 a {
	background: url(img/nav-left.png) no-repeat left;
}

#m2 span {
	background: #99AB63 url(img/nav-right.png) no-repeat right;
}

#left-column {
	width: auto;
}
</style>

</head>
<body>
<div id="wrap">
 <?php include "../header.php"?>
<div id="content">
<div id="left-column">
<div class="ui-widget">
    <label for="from">Desde: <input type="text" id="from" name="from" /></label>
    <label for="to">hasta: <input type="text" id="to" name="to" /></label>

</div>
<div id="leftcol">
<ul>
<?php while( $row_rsArticle= $rsArticles->fetch_assoc()){ ?>
	<li><label><input name="selected[]" type="checkbox" value="<?php echo $row_rsArticle["idarticulo"] ?>" /> <?php echo utf8tohtml($row_rsArticle["descripcion"], True) ?></label></li>
<?php } ?>
</ul>
</div>
<div id="canvas"></div>
<?php include "../footer.php" ?></div>
</div>

</div>

</body>
</html>
