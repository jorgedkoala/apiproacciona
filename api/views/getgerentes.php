<?php 
header('Access-Control-Allow-Origin: *');  
include("../config/conexion2.php");

$idempresa = $_GET["idempresa"];

$registros=mysqli_query($conexion,"select email from usuarios where tipouser='Gerente' AND  idempresa = '" . $idempresa . "'") or die("{'success':false,'error':".mysqli_error($conexion)."}");
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