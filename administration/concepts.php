<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para administrar conceptos");
}
if(isset($_POST["add"])){
	$cname = $dbc->real_escape_string(trim($_POST["cname"]));
	$moduleid = (int)$_POST["module"];
	if($cname && array_key_exists($moduleid, $modules)){
		$query = "INSERT INTO conceptos (descripcion, modulo) VALUES ('$cname', $moduleid)";
		$dbc->query($query);
		$status = "<p>Se a&ntilde;adio un concepto</p>";
	}
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
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript">
$(function(){
	$("#add").click(function(e){
		$(".addconcept").toggle();		
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
		<h1>Conceptos</h1>
		<a href="#" id="add">A&ntilde;adir Concepto</a>
		<div class="addconcept  hide">
			<form class="cform" method="post" action="administration/concepts.php">
				<p>
					<label><span>Nombre del concepto:</span> <input type="text" name="cname" /></label>
				</p>
					<p>Modulo :</p>
					<?php foreach($modules as $index => $module){ ?>
					<p>
						<label><?php echo $module ?><input type="radio" name="module" value="<?php echo $index ?>" /></label>
					</p>
					<?php } ?>
				</p>
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