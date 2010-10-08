<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
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
        SELECT
            SUM(axd.unidades) + IFNULL(
            (
            SELECT SUM(axdi.unidades)
            FROM articulosxdocumento axdi
            JOIN documentos di ON di.iddocumento = axdi.iddocumento
            WHERE idarticulo = axd.idarticulo AND  di.fechacreacion < d.fechacreacion
             ),0) as unidades,
            SUM(axd.unidades) as diferencia,
            UNIX_TIMESTAMP(d.fechacreacion) * 1000 as  stamp,
            v.descripcion as nombre,
            CONCAT_WS(' ',td.descripcion,d.ndocimpreso) as ndocimpreso
        FROM articulosxdocumento axd
        JOIN documentos d ON axd.iddocumento = d.iddocumento
        JOIN tiposdoc td  ON td.idtipodoc = d.idtipodoc
        JOIN vw_articulosdescritos v ON v.idarticulo = axd.idarticulo
        WHERE  v.idarticulo = $d -- AND d.idestado IN ( {$docstates["CONFIRMADO"]}, {$docstates["ANULADO"]})
        -- AND d.idtipodoc IN ({$docids["ENTRADALOCAL"]},{$docids["KARDEX"]},{$docids["LIQUIDACION"]},{$docids["FACTURA"]})
        GROUP BY d.iddocumento
        ORDER BY d.fechacreacion
        ");
        $data = $dbc->query($query);
        $inner = array();

        if($data->num_rows){
            while ($row = $data->fetch_array(MYSQLI_ASSOC)) {
                $label = $row["nombre"];
                $inner[] = array( $row["stamp"],$row["unidades"], $row["diferencia"], $row["ndocimpreso"]);
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

<!-- Descargar la imagen -->
<script type="text/javascript" src="js/jq.flot.text.js"></script>
<script type="text/javascript" src="js/base64.js"></script>
<script type="text/javascript" src="js/canvas2image.js"></script>
<script type="text/javascript">
var jsonaddress = "<?php echo $basedir ?>reports/articlesmoves.php";
var moneysimbol = "US$";

$(function(){
//     $.getScript("js/linegraph.js");

    var placeholder = $("#canvas");

    var mmToMonth = new Array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

    function showLocalDate(timestamp){
      var dt = new Date(timestamp);
      var mm = mmToMonth[dt.getMonth()];
      return dt.getDate()+ "-"+ mm+ "-" +dt.getFullYear();
    }
    
var options = {
        lines:{show:true},
        points:{show:true},
        xaxis: {
        mode:'time',
        timeformat: "%d-%m-%y",
        monthNames : mmToMonth,
        ticks:5


        },
        yaxis: {
            tickFormatter: function(val, axis){
                return val;
            },
            labelWidth: 80,
            backgroundColor:"#000",
            color:"#000",
            tickColor: "#000"
        },
        minTickSize: [1, "day"],
        grid: {
            hoverable: true,
            canvasText: {show: true, font:"sans 8px" },
            backgroundColor:"#fff"
        }

};
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


    $("input:checkbox").click(function(){
    var startdate = $("#from").val();
    var enddate = $("#to").val();
    var selected = [];
    var ui= $("input:checkbox:checked");
    $.each(ui,function(i,val){
        selected.push(val.value);
    });
    selected = selected.toString();
    $.ajax({
        url:jsonaddress ,
        dataType:'json',
        data:{
        "ac": selected,
        "startDate": startdate,
        "endDate": enddate
        },
        success:function(data){
        $.plot(placeholder, data, options)
        }
    });
    var previousPoint = null;
    placeholder.bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));
            if (item) {
                if (previousPoint != item.datapoint) {
                    previousPoint = item.datapoint;

                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
                        diferencia = item.series.data[item.dataIndex][2];
                        if( diferencia > 0){
                            diferencia = "+" + diferencia;
                        }
                    showTooltip(item.pageX, item.pageY,
                                 item.datapoint[1] + " / " + diferencia +" / " + showLocalDate(item.datapoint[0])  + " / " + item.series.data[item.dataIndex][3]
                                 );

                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;
            }

    });
});
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
