<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("gerencia"))){
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
		SELECT v.idarticulo,SUM(c.valor) as total, UNIX_TIMESTAMP(tc.fecha)*1000 as stamp, v.descripcion as nombre
		FROM costosarticulo c
		JOIN vw_articulosdescritos v ON c.idarticulo = v.idarticulo
		JOIN tiposcambio tc ON tc.idtc = c.idtc
		WHERE c.idarticulo = $d ".
			( $startDate ? " AND fecha  >= '" . $startDate ."'": "" )
			.( $endDate ? " AND  fecha <= '" . $endDate . "'": "" )
			." GROUP BY tc.fecha
		ORDER BY fecha
		");
			$data = $dbc->query($query);
			$inner = array();

			if($data->num_rows){
				while ($row = $data->fetch_array(MYSQLI_ASSOC)) {
					$label = $row["nombre"];
					$inner[] = array( (float)$row["stamp"],(float)$row["total"]);
				}
				$return[] = array("label"=>$label,"data"=>$inner);
			}
		}
	}
	echo json_encode($return);
	die();
}
$rsArticles = $dbc->query("SELECT idarticulo , descripcion FROM vw_articulosdescritos v LIMIT 0,1000");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
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

<!-- Descargar la imagen -->
<script type="text/javascript" src="js/jq.flot.text.js"></script>
<script type="text/javascript" src="js/base64.js"></script>
<script type="text/javascript" src="js/canvas2image.js"></script>
<script type="text/javascript">
var jsonaddress = "<?php echo $basedir ?>reports/articlescosts.php";
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
<div class="ui-widget"><label for="from">Desde: </label> <input
	type="text" id="from" name="from" /> <label for="to">hasta: </label> <input
	type="text" id="to" name="to" /></div>
<div id="leftcol">
<ul>
<?php while( $row_rsArticle= $rsArticles->fetch_assoc()){ ?>
	<li><label><input name="selected[]" type="checkbox" value="<?php echo $row_rsArticle["idarticulo"] ?>" /> <?php echo utf8tohtml($row_rsArticle["descripcion"], True) ?></label></li>
<?php } ?>
</ul>
</div>
<div id="canvas"></div>
</div>
<?php include "../footer.php" ?></div>
</div>

</body>
</html>
