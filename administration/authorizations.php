<?php
/**
* @package administration
*/
require_once "../functions.php";
try{
    if (!$_SESSION["user"]->hasRole("gerencia")) {
        die("Usted no tiene permisos para administrar autorizaciones");
    }
    $authid = (int) $_GET["doc"]; // El id de la factura de credito a autorizar
    $del = (int) $_GET["del"];// El id de la factura de credito a denegar


    $anullmentid = (int)$_GET["adoc"]; // el id de la factura a anular
    $anullmentdel = (int)$_GET["adel"]; // el id de la factura a la cual se le denega la anulación

    $devolutionid = (int)$_GET["devdoc"]; // el id de la devolucion a autorizar
    $devolutiondel = (int)$_GET["devdel"]; // el id de la devolución a denegar

    if ($authid) {
        //autorizar una factura de credito
        $query = "
        CALL spAutorizarFactura(
        $authid,
        {$_SESSION["user"]->getUid()},
        {$persontypes["SUPERVISOR"]},
        {$docstates["CONFIRMADO"]},
        {$accounts["VENTASNETAS"]},
        {$accounts["CXCCLIENTE"]},
        {$accounts["INVENTARIO"]},
        {$accounts["COSTOSVENTAS"]},
        {$accounts["IMPUESTOSXPAGAR"]}
        )";
        $result = $dbc->multi_query($query);

        if ($result) {
            $print = "<p>La factura se ha autorizado</p>";
        } else {
            $print = "<p class'error'>Hubo un error al autorizar la factura</p>";
        }
        //$result->free_result();
    } elseif ($del) {
        //denegar un credito
        $result = $dbc->multi_query("
        CALL spEliminarFactura($del)
        ");
        if ($result) {
            $print = "<p>La factura se ha denegado</p>";
        } else {
            $print = "<p class'error'>Hubo un error al denegar la factura</p>";
        }
        //$result->free_result();
    }elseif($anullmentid){
        $query="
            CALL spAutorizarAnulacionFactura(
            $anullmentid,
            {$_SESSION["user"]->getUid()},
            {$docids["ANULACION"]},
            {$docids["FACTURA"]},
            {$docids["RECIBO"]},
            {$docids["KARDEX"]},
            {$docstates["PENDIENTEANULACION"]},
            {$docstates["CONFIRMADO"]},
            {$docstates["ANULADO"]},
            {$persontypes["SUPERVISOR"]}
            )
            ";
        $result = $dbc->multi_query($query);

            if ($result) {
            $print = "<p>La Anulaci&oacute;n se ha autorizado</p>";
            } else {
                $print = "<p class'error'>Hubo un error al autorizar la Anulaci&oacute;n </p>";
            }

    }elseif($anullmentdel){
        $query="
        CALL spDenegarAnulacion(
        $anullmentdel,
        {$docstates["CONFIRMADO"]},
        {$docids["ANULACION"]}
        );
        ";
        $result = $dbc->multi_query($query);

        if ($result) {
        $print = "<p>Se ha denegado la anulaci&oacute;</p>";
        } else {
            $print = "<p class'error'>Hubo un error al denegar la Anulaci&oacute;n </p>";
        }

    }elseif($devolutionid){
        $query="
            CALL spAutorizarDevolucion(
            $devolutionid,
            {$_SESSION["user"]->getUid()},
            {$persontypes["SUPERVISOR"]},
            {$docids["NOTACREDITO"]},
            {$docids["FACTURA"]},
            {$docids["CIERRE"]},
            {$docids["RECIBO"]},
            {$docstates["CONFIRMADO"]},
            {$docstates["PENDIENTE"]},
            {$accounts["VENTASNETAS"]},
            {$accounts["COSTOSVENTAS"]},
            {$accounts["IMPUESTOSXPAGAR"]},
            {$accounts["RETENCIONPAGADA"]},
            {$accounts["CAJA"]},
            {$accounts["INVENTARIO"]}
            )
            ";
        echo $query;
        $result = $dbc->multi_query($query);
            if ($result) {
            $print = "<p>La Devoluci&oacute;n se ha autorizado</p>";
            } else {
                $print = "<p class'error'>Hubo un error al autorizar la Devoluci&oacute;n </p>";
            }
    }

    while($dbc->more_results())
    {
        $dbc->next_result();
        $discard = $dbc->store_result();
    }
    $query = "
    SELECT
        d.iddocumento,
        d.idtipodoc,
        td.descripcion,
        p.nombre
    FROM documentos d
    JOIN tiposdoc td ON d.idtipodoc = td.idtipodoc
    JOIN creditos cr ON cr.iddocumento = d.iddocumento
    JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
    JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["CLIENTE"]}
    WHERE d.idestado = {$docstates["PENDIENTE"]} AND d.idtipodoc = {$docids["FACTURA"]}
    ORDER BY d.iddocumento
    ";
    $rsCreditInvoices = $dbc->query($query);

    $query = "
    SELECT
        fact.iddocumento,
        fact.ndocimpreso,
        d.iddocumento as idanulacion,
        d.idtipodoc,
        td.descripcion,
        p.nombre
    FROM documentos d
    JOIN docpadrehijos dpd ON dpd.idhijo = d.iddocumento
    JOIN documentos fact ON fact.iddocumento = dpd.idpadre AND fact.idtipodoc = {$docids["FACTURA"]}
    JOIN tiposdoc td ON d.idtipodoc = td.idtipodoc
    JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento AND pxd.idaccion = {$persontypes["USUARIO"]}
    JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["USUARIO"]}
    WHERE d.idtipodoc = {$docids["ANULACION"]} AND d.idestado = {$docstates["PENDIENTE"]}
    ORDER BY d.iddocumento
    ";
    $rsAnullments = $dbc->query($query);

    $query = "
            SELECT
            d.iddocumento,
            d.ndocimpreso,
            fact.ndocimpreso as nfactura,
            d.idtipodoc,
            td.descripcion,
            p.nombre
        FROM documentos d
        JOIN docpadrehijos dpd ON dpd.idhijo = d.iddocumento
        JOIN documentos fact ON dpd.idpadre = fact.iddocumento AND fact.idtipodoc = {$docids["FACTURA"]}
        JOIN tiposdoc td ON d.idtipodoc = td.idtipodoc
        JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento AND pxd.idaccion = {$persontypes["USUARIO"]}
        JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["USUARIO"]}
        WHERE d.idtipodoc = {$docids["NOTACREDITO"]} AND d.idestado = {$docstates["PENDIENTE"]}
        ORDER BY d.iddocumento
     ";
     $rsDevolutions = $dbc->query($query);
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
<?php include "../header.php" ?>
<div id="content">
<?php echo $print ?>
<h1>Documentos pendientes de autorizaci&oacute;n</h1>

<?php if ($rsCreditInvoices->num_rows) { ?>
    <h2>Creditos de factura</h2>
    <ul>
        <?php while ($row_rsDocument = $rsCreditInvoices->fetch_array(MYSQLI_ASSOC)) { ?>
            <li>
                <a href="<?php echo $base ?>reports/facturas.php?doc=<?php echo $row_rsDocument["iddocumento"] ?>">
                    <?php echo $row_rsDocument["descripcion"] ?> para <?php echo $row_rsDocument["nombre"] ?>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php } ?>


<?php if ($rsAnullments->num_rows) { ?>
    <h2>Anulaciones de  factura</h2>
    <ul>
        <?php while ($row_rsDocument = $rsAnullments->fetch_array(MYSQLI_ASSOC)) { ?>
            <li>
                <a href="<?php echo $base ?>reports/facturas.php?doc=<?php echo $row_rsDocument["iddocumento"] ?>">
                    <?php echo $row_rsDocument["descripcion"]. "  " . $row_rsDocument["ndocimpreso"] ?>  por <?php echo $row_rsDocument["nombre"]; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php } ?>

<?php if ($rsDevolutions->num_rows) {
?>
    <h2>Devoluciones de factura</h2>
    <ul>
    <?php while ($row_rsDocument = $rsDevolutions->fetch_array(MYSQLI_ASSOC)) { ?>
        <li>
            <a href="<?php echo $base ?>reports/devoluciones.php?doc=<?php echo $row_rsDocument["iddocumento"] ?>">
                Devoluci&oacute;n  por <?php echo $row_rsDocument["nombre"]; ?>
            </a>
        </li>
    <?php } ?>
    </ul>
<?php } ?>



<?php if (!( $rsAnullments->num_rows + $rsCreditInvoices->num_rows + $rsDevolutions->num_rows)) { ?>
    <p>No hay documentos pendientes de autorizaci&oacute;n</p>
<?php } ?>




                </div>
<?php include "../footer.php" ?>
        </div>
    </body>
</html>