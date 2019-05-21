<?php 
header('Access-Control-Allow-Origin: *');  
include("../config/conexion2.php");
$method = $_SERVER['REQUEST_METHOD'];
$idempresa = $_GET["idempresa"];
$version='';
if ($_GET["version"]){
$version = $_GET["version"];
}
//$sql = "SELECT * FROM permissionuserchecklist inner JOIN checklist on permissionuserchecklist.idchecklist = checklist.id inner JOIN controlchecklist ON checklist.id = controlchecklist.idchecklist WHERE checklist.idempresa = '" . $idempresa . "'";

$WHERE ='';
if($_GET["WHERE_USER"]){
  $WHERE = " AND pl.idusuario = '" .  mysqli_real_escape_string($conexion,$_GET["WHERE_USER"]) . "'";
}
//echo $sql;
switch ($method) {
  case 'GET':
  if ($version == ''){
   $sql = "select M.id as idMaquina, M.nombre as nombreMaquina, mm.*, pm.idusuario FROM maquinaria M INNER JOIN maquina_mantenimiento mm ON M.id = mm.idmaquina INNER JOIN permissionMaquinaria pm ON mm.id = pm.idmantenimiento  WHERE M.idempresa=$idempresa" .$WHERE . " ORDER BY M.nombre, mm.orden"; break;
  }else{
   $sql = "select M.id as idMaquina, M.nombre as nombreMaquina, mm.*, pl.id as idpermiso, pl.idusuario, pl.idmantenimiento as idmantenimientopermiso FROM maquinaria M INNER JOIN maquina_mantenimiento mm ON M.id = mm.idmaquina INNER JOIN permissionMaquinaria pl ON mm.id = pl.idmantenimiento  WHERE M.idempresa=$idempresa AND pl.idempresa = $idempresa " .$WHERE . " ORDER BY M.nombre, mm.orden"; break;
   }
}
$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->"'.mysqli_error($conexion).'" ,"sql":"'.$sql.'"}');

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