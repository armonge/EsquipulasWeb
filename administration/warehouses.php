<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para administrar bodegas");
}
if(isset($_POST["add"])){
	$wname = $dbc->real_escape_string(trim($_POST["wname"]));
	if($wname){
		$query = "INSERT INTO bodegas (nombrebodega) VALUES ('$wname')";
		$dbc->query($query);
		$status = "<p>Se a&ntilde;adio una bodega</p>";
	}
}

$rsWarehouses = $dbc->query("
SELECT b.idbodega, b.nombrebodega
FROM bodegas b
")

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
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript">
$(function(){
	$("#add").click(function(e){
		$(".addwarehouse").toggle()
		return false;
	});

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
		<h1>Bodegas</h1>
		<?php echo $status ?>
		<ul>
			<?php while($row_rsWarehouse = $rsWarehouses->fetch_array(MYSQLI_ASSOC)){ ?>
				  <li><?php echo $row_rsWarehouse["nombrebodega"]?>
				  </li>
			<?php } ?>
		</ul>
		<a href="#" id="add">A&ntilde;adir Bodega</a>
		<div class="hide addwarehouse">
			<form class="cform" method="post" action="administration/warehouses.php">
				<p>
					<label><span>Nombre de la bodega:</span> <input type="text" name="wname" /></label>
				</p>
				<p>
					<input type="submit" value="Aceptar" />
					<input type="hidden" name="add" value="yes" />
				</p>
			</form>
		</div>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>