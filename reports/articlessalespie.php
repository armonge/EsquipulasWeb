<?php
require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("ventasrep") || $_SESSION["user"]->hasRole("gerencia"))){
    die("Usted no tiene permisos para acceder a este modulo");
}
if(isset($_GET["date"])){
    list($month , $year, $rest) = explode(' ', $_GET["date"], 3);
    $month = (int)$month;
    $year = (int)$year;

    $return = array();
        $query= ("
            SELECT
                a.descripcion as label,
                axd.unidades * axd.precioventa * -1 AS data
            FROM articulosxdocumento axd
            JOIN vw_articulosdescritos a ON axd.idarticulo = a.idarticulo
            JOIN documentos d ON d.iddocumento = axd.iddocumento AND d.idestado = 1 AND d.idtipodoc = 5
            WHERE MONTH(d.fechacreacion) = $month AND YEAR(d.fechacreacion) = $year
            GROUP BY axd.idarticulo, MONTH(d.fechacreacion)
            LIMIT 5
        ");
        
        $data = $dbc->query($query);
        $return = array();
        
        if($data->num_rows){
            while ($row = $data->fetch_array(MYSQLI_ASSOC)) {
                $return[] = array("label"=>$row["label"],"data"=>(int)$row["data"]);
            }
            
        }
    echo json_encode($return);
    die();
}
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
<script type="text/javascript" src="js/jq.flot.pie.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-es.js"></script>
<script type="text/javascript">
var jsonaddress = "<?php echo $basedir ?>reports/articlessalespie.php";
var pie = {
    show: true,
    pieStrokeLineWidth: 0,
    pieStrokeColor: '#FFF',
    showLabel: true,
    labelOffsetFactor: 5/6,
    labelBackgroundOpacity: 0.55,
    labelFormatter: function(serie){
        /*<![CDATA[ */
    return serie.label+'\<br\/>'+Math.round(serie.percent)+'%';
        /*]]>*/
    }
};
$(function(){
        
    var legend = {
        show: true,
        position: "ne",
        backgroundOpacity: 0,
        container : $("#legend")
    };
    var placeholder = $("#canvas");


    $('#date').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: false,
        dateFormat: 'mm yy',
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
    $("#dateform").submit(function(){
        updateGraph()
        return false;
    });
    function updateGraph(){
        $.getJSON(
            jsonaddress,
            {date :$("#date").val()},
            function(data){
                $.plot(placeholder, data, {'pie':pie,'legend':legend});
            }
        )
    }
    updateGraph()

    

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
.ui-datepicker-calendar {
    display: none;
    }
</style>

</head>
<body>
<div id="wrap">
 <?php include "../header.php"?>
<div id="content">
<div id="left-column">
<form action="reports/articlessalespie.php" id="dateform">
<div class="ui-widget">
    <label for="date">Para: <input type="text" id="date" name="date" value="<?php echo date("m Y") ?>" /></label>
    <input type="submit" value="aceptar" />
</div>

</form>
<div id="canvas"></div>
<div id="legend"></div>
<?php include "../footer.php" ?></div>
</div>

</div>

</body>
</html>
