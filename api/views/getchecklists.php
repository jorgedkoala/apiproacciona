<?php 
header('Access-Control-Allow-Origin: *');  
include("../config/conexion2.php");

$idempresa = $_GET["idempresa"];
$sql = "SELECT * FROM permissionuserchecklist inner JOIN checklist on permissionuserchecklist.idchecklist = checklist.id inner JOIN controlchecklist ON checklist.id = controlchecklist.idchecklist WHERE controlchecklist.migrado =0 AND checklist.idempresa = '" . $idempresa . "' ORDER BY checklist.orden, controlchecklist.orden";
$registros=mysqli_query($conexion,$sql) or die("{'success':false,'error':".mysqli_error($conexion)."}");
//echo $sql;
while ($reg=mysqli_fetch_array($registros))
{	
$rows[] = $reg;
}

if($registros){
$result = '{"success":"true","data":' . json_encode($rows) . '}';
}
else {
$result = '{"success":"false"}';
}
print json_encode($result);

?>