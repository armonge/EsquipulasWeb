<?php
/**
* @package administration
*/
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
    die("Usted no tiene permisos para administrar conceptos");
}
try{
    
    if(isset($_POST["add"]) && $_POST["add"] == 1 ){
        $type = (int)$_POST["doctype"];
        $name = $dbc->real_escape_string($_POST["name"]);
        $result = $dbc->query("INSERT INTO conceptos (descripcion, idtipodoc) VALUES ('$name', $type);");
        echo json_encode(array(
            "result" => $result,
            "data" => $name
        ));
        die();
    }


    $query = "
    SELECT descripcion, idtipodoc FROM conceptos
    ";
    $rsConcepts = $dbc->query($query);
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
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas: Administraci&oacute;n</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript">
$(function(){
    $("#docs").change(function(){
        selection = $("#docs option:selected").attr('id');
        $("#concepts option").hide();
        $("." + selection).show()
    });
    $("#concepts option").hide();
        $("#dialog-form").dialog({
            autoOpen: false,
//             height: 300,
            width: 400,
            modal: true,
            buttons: {
                Aceptar:function(){
                    if( $("#dialog-form input").val() != ""){
                        var docid = $("#docs option:selected").val();
                        var name = $("#dialog-form input").val();
                        $.ajax({
                            url:"<?php echo $basedir ?>administration/concepts.php",
                            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                            dataType:'json',
                            type:'POST',
                            success:function(data){
                                if(data.result){
                                    /*<![CDATA[*/
                                    $("#concepts").append('\<option class="doc_' + docid + '">' + data.data + '\<\/option>');
                                    /*]]>*/
                                    $("#dialog-form input").val('');
                                    $("#dialog-form").dialog('close');
                                }else{
                                    $dialog_error.dialog('open');
                                }

                            },
                            data:{
                                "doctype":docid,
                                "name":name,
                                "add":1
                            },
                            error:function(XMLHttpRequest, textStatus, errorThrown){
                                 $dialog_error.dialog('open');
                                 $(this).dialog('close');
                            }
                        });
                    }else{
                        $("#dialog-form .error").fadeIn(function(){
                            setTimeout("$('#dialog-form .error').fadeOut()", 3000);
                        });
                    }

                },
                Cancelar: function() {
                    $(this).dialog('close');
                }
            }
        });
        var $dialog_error = $("#dialog-error").dialog({
            autoOpen: false,
            modal:true
        });

        $('#add')
            .button()
            .click(function() {
                if( $("#docs option:selected").val() != undefined ){
                    $('#dialog-form').dialog('open');
                }else{
                    $('#dialog-choose').dialog({
                        modal:true
                    })
                }
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
#concepts, #docs{
    width:200px;
    height:100px;
}
#concepts{
    width:350px;
}
#add{
    font-weight:bold;
    font-size:14px;
}
.cforms{
    float:left;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
    <h1>Conceptos</h1>
    <select size="5" id="docs">
            <option id="doc_2" value="2"> ANULACION </option>
            <option id="doc_5" value="5"> FACTURA </option>
            <option id="doc_10" value="10"> DEVOLUCION </option>
            <option id="doc_11" value="11"> NOTADEBITO </option>
            <option id="doc_12" value="12"> CHEQUE </option>
            <option id="doc_13" value="13"> DEPOSITO </option>
            <option id="doc_14" value="14"> NOTACREDITO </option>
            <option id="doc_18" value="18"> RECIBO </option>
            <option id="doc_19" value="19"> RETENCION </option>
            <option id="doc_24" value="24"> AJUSTECONTABLE </option>
            <option id="doc_25" value="25"> CONCILIACION </option>
            <option id="doc_26" value="26"> ERROR </option>
            <option id="doc_27" value="27"> KARDEX</option>
            <option id="doc_30" value="30"> AJUSTE DE BODEGA</option>
    </select>
    <select size="5" id="concepts">
    <?php while($row_rsConcepts = $rsConcepts->fetch_array(MYSQLI_ASSOC)){ ?>
        <option class="doc_<?php echo $row_rsConcepts["idtipodoc"] ?>"> <?php echo utf8tohtml($row_rsConcepts["descripcion"]) ?> </option>
    <?php } ?>
    </select>
    <button id="add"><img src="img/list-add.png" alt="A&ntilde;adir concepto" /> A&ntilde;adir concepto</button >



    <div id="dialog-form" title="A&ntilde;adir un concepto">
    <div class="cforms">
    <p>
        <label><span>Concepto:</span> <input type="text" name="cname" /></label>
        
    </p>
    <p class="error hide"> El concepto esta vacio</p>
    </div>
    </div>
    <div id="dialog-choose" class="hide" title="Seleccione un tipo de documento">
        <p>
        Si desea a&ntilde;adir un concepto primero seleccione un tipo de documento
        </p>
    </div>
    <div id="dialog-error" class="hide" title="Error">
        <p>
        Hubo un error al a&ntilde;adir el concepto
        </p>
    </div>


</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>