<?php 
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT"); 
include("../config/conexion2.php");

$method = $_SERVER['REQUEST_METHOD'];
$idempresa = $_GET["idempresa"];
$userId = $_GET["userId"];

switch ($method) {
  case 'GET':
    $sql = "select updateusers,updatecontrols from empresas WHERE id=$idempresa"; break;
}
$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'"}');

if ($method == 'GET') {
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
}
print json_encode($result);

?>