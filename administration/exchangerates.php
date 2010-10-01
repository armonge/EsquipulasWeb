<?php
/**
* @package administration
*/
require_once "../functions.php";
try{
    if(!$_SESSION["user"]->hasRole("gerencia")){
        die("Usted no tiene permisos para administrar tipos de cambio");
    }
    if(isset($_GET["edit"])){
        $idtc = (int)$_POST["id"];
        $banco = (double)$_POST["Banco"];

        if($idtc){
            $query ="UPDATE tiposcambio SET tasabanco = $banco  WHERE idtc = $idtc AND fecha > CURDATE() LIMIT 1";
            if($dbc->query($query) && $dbc->affected_rows){
                $status = array("result"=>true);

            }else{
                $status = array("result"=>false);
            }
            echo json_encode($status);
        }
        die();
    }

    if(isset($_GET["nav"])){
        $page = $_GET['page'];

        // get how many rows we want to have into the grid - rowNum parameter in the grid
        $limit = $_GET['rows'];

        // get index row - i.e. user click to sort. At first time sortname parameter -
        // after that the index from colModel
        $sidx = $_GET['sidx'];

        // sorting order - at first time sortorder
        $sord = $_GET['sord'];

        // if we not pass at first time index use the first column for the index or what you want
        if(!$sidx) $sidx =1;

        // calculate the number of rows for the query. We need this for paging the result
        $result = $dbc->query("SELECT COUNT(idtc) AS count FROM tiposcambio");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $count = $row['count'];

        // calculate the total pages for the query
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }

        // if for some reasons the requested page is greater than the total
        // set the requested page to total page
        if ($page > $total_pages) $page=$total_pages;

        // calculate the starting position of the rows
        $start = $limit*$page - $limit;

        // if for some reasons start position is negative set it to 0
        // typical case is that the user type 0 for the requested page
        if($start <0) $start = 0;

        // the actual query for the grid data
        $SQL = "
            SELECT
                idtc,
                UNIX_TIMESTAMP(fecha)*1000 as stamp,
                DATE_FORMAT(fecha, '%d/%c/%Y') as fecha,
                tasa,
                IFNULL(tasabanco,0) as tasabanco
            FROM tiposcambio
            ORDER BY tiposcambio.$sidx  $sord
            LIMIT $start , $limit
        ";
        $result = $dbc->query( $SQL );
        $responce->page = $page;
        $responce->records = $count;
        $responce->total = $total_pages;
        // be sure to put text data in CDATA
        $i=0;
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $responce->rows[$i]['id']=$row["idtc"];
            $responce->rows[$i]['cell'] = array(
            $row["idtc"],
            $row["stamp"],
            $row["fecha"],
            $row["tasa"],
            $row["tasabanco"]
            );
            $i++;
        }

        header('Content-type: application/json');
        echo json_encode($responce);

        die();

    }
}catch(EsquipulasException $ex){
    if($local){
        die($ex);
    }else{
        $ex->mail(ADMINMAIL);
        header("Location: {$basedir}error.php ");
        die();
    }
}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php  echo $basedir ?>;favicon.ico" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas: Administraci&oacute;n</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link type="text/css" href="css/flick/jq.ui.css" rel="Stylesheet" />
<link rel="stylesheet" type="text/css" href="css/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript" src="js/grid.locale-sp.js"></script>
<script type="text/javascript" src="js/jq.jqgrid.js"></script>
<style type="text/css">
#m3 a {
	background: url(img/nav-left.png) no-repeat left;
}

#m3 span {
	background: #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
    $.jgrid.no_legacy_api = true;

    var lastsel;
    var isroweditable = function (id) {
		// implement your criteria here
		data = $("#list").getRowData(id);
		rowdate = new Date(parseInt(data.stamp));
		curdate = new Date();
		if(rowdate < curdate){
		    return false;
		}else{
		    return true;
		}
    };
    $("#list").jqGrid({
	url:'<?php echo $basedir ?>administration/exchangerates.php?nav=yes',
	editurl:'<?php echo $basedir ?>administration/exchangerates.php?edit=yes',
	datatype: 'json',
	colNames:['Id','stamp','Fecha', 'Oficial','Banco'],
	altRows:true,
	colModel :[
	    {name:'Id', index:'idtc', width:55, hidden:true},
	    {name:'stamp', index:'stamp', hidden:true},
	    {name:'Fecha', index:'fecha', width:100},
	    {name:'Oficial', index:'tasa', width:100, align:'right'},
	    {name:'Banco', index:'tasabanco', width:100, align:'right',editable:true,editrules:{number:true}}
	],
	onSelectRow: function(id){
	    if(id && id!==lastsel){
			$('#list').jqGrid('restoreRow',lastsel);
			
			if (isroweditable(id)) {
			    $('#list').jqGrid('editRow',id,true);
			    lastsel=id;
			   
			}

	    }
	},
	height:300,
	width:550,
	pager: '#pager',
	rowNum:10,
	rowList:[10,20,50],
	sortname: 'fecha',
	sortorder: 'desc',
	viewrecords: true,
	caption: 'Tipos de Cambio'
    });
});
</script>

</head>
<body>
<div id="wrap"><?php include "../header.php"?>
<div id="content">
<table id="list">
	<tr>
		<th>Fecha</th>
		<th>Oficial</th>
		<th>Cambio</th>
	</tr>
</table>
<div id="pager"></div>
</div>
<?php include "../footer.php" ?></div>
</body>
</html>
