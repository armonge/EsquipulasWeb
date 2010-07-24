<?php
include "functions.php";
$rsInfo = $dbc->query("
SELECT T.TABLE_NAME, C.COLUMN_NAME, C.COLUMN_COMMENT, C.COLUMN_TYPE, K.CONSTRAINT_NAME, C.IS_NULLABLE
FROM information_schema.`TABLES` T
JOIN information_schema.COLUMNS C ON C.TABLE_NAME = T.TABLE_NAME AND C.TABLE_SCHEMA = T.TABLE_SCHEMA
LEFT JOIN information_schema.KEY_COLUMN_USAGE K ON K.TABLE_SCHEMA = C.TABLE_SCHEMA AND K.COLUMN_NAME = C.COLUMN_NAME AND K.TABLE_NAME = C.TABLE_NAME
WHERE C.TABLE_SCHEMA ='esquipulasdb' AND ENGINE IS NOT NULL
GROUP BY C.COLUMN_NAME, T.TABLE_NAME
ORDER BY T.TABLE_NAME
");
$row_rsInfo = $rsInfo->fetch_array(MYSQLI_ASSOC);
$table = "";
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
</head>
<body>
<?php do{
if($table != $row_rsInfo["TABLE_NAME"]){
    echo "
    </table>{$row_rsInfo["TABLE_NAME"]}<table>
    <tr>
	<th>Nombre</th>
	<th>Definici&oacute;n</th>
	<th>Tipo de dato</th>
	<th>PK/FK</th>
	<th>Opci&oacute;n NULL</th>
    </tr>
    ";
}
$table = $row_rsInfo["TABLE_NAME"];
?>
<tr>
<td><?php echo $row_rsInfo["COLUMN_NAME"] ?></td>
<td><?php echo utf8tohtml($row_rsInfo["COLUMN_COMMENT"], true)  ?></td>
<td><?php echo $row_rsInfo["COLUMN_TYPE"] ?></td>
<td><?php
$print ="" ;
if(strstr($row_rsInfo["CONSTRAINT_NAME"], "PRIMARY")){
    $print = "PK";
}elseif($row_rsInfo["CONSTRAINT_NAME"]){ //strstr($row_rsInfo["CONSTRAINT_NAME"], "fk")){
    $print = "FK";
}
echo $print;
 ?></td>
 <td><?php  if($row_rsInfo["IS_NULLABLE"]=="YES"){echo "IS NULL"; } else{ echo "NOT NULL"; } ?></td>
<?php }while($row_rsInfo = $rsInfo->fetch_array(MYSQLI_ASSOC) ) ?>

</table>
</body>
</html>