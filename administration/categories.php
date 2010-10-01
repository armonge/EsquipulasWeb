<?php
/**
* @package administration
*/
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para administrar categorias");
}
$rsL1 = $dbc->query("
SELECT p.idcategoria, p.nombre, p.padre, COUNT(h.idcategoria) as hijos
FROM categorias p
LEFT JOIN categorias h ON h.padre = p.idcategoria
WHERE p.padre IS NULL
GROUP BY idcategoria
");

function drawTree($categorie){
global $dbc;
echo "<li id='rhtml_{$categorie["idcategoria"]}'>\n";
echo "<a href='#' >{$categorie["nombre"]}</a>";
if($categorie["hijos"] >0){
$query = "
	SELECT p.idcategoria, p.nombre, p.padre, COUNT(h.idcategoria) as hijos
	FROM categorias p
	LEFT JOIN categorias h ON h.padre = p.idcategoria
	WHERE p.padre = {$categorie["idcategoria"]}
	GROUP BY p.idcategoria
";
$rsChilds = $dbc->query($query);
echo "<ul>\n";
	while($row_rsChild = $rsChilds->fetch_array(MYSQLI_ASSOC)){
		drawTree($row_rsChild);
	}
echo "</ul>\n";
}
echo "</li>\n";
}
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
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<script type="text/javascript" src="js/jq.js"></script>
<script  type="text/javascript" src="js/jquery.jstree.js"></script>
<script type="text/javascript">
$(function () {
	$.jstree._themes="<?php echo $basedir ?>css/jstree/";
	$("#tree").jstree({ 
			dnd : {
				"drop_finish" : function () { 
						alert("DROP"); 
				},
				"drop_target":false,
				"drag_target":false,
				"drop_check" : function (data) {
						return false;
				},
				"drag_check" : function (data) {
						return {
								after:false,
								before:false,
								inside:false
						}
				},
				"drag_finish" : function () { 
					alert("DRAG OK"); 
				}
			},

		"plugins" : [ "themes", "html_data", "ui", "crrm", "contextmenu","dnd" ]
	});
	$("#tree").jstree("toggle_icons");
	$("#tree").jstree("toggle_dots");
});

</script>
<style type="text/css">
#m3 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m3 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
	<div id="left-column">
		<h1>Categorias</h1>
		<?php echo $status ?>
		<ul id="tree">
			<?php while($row_rsL1 = $rsL1->fetch_array(MYSQLI_ASSOC)){
				echo drawTree($row_rsL1);
			} ?>
		</ul>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
