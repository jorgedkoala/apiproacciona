<?php 
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT"); 
include("../config/conexion2.php");
///CONPROBAR TOKEN
///CONPROBAR TOKEN
require_once('../jwt/jwt2.php');
use \Firebase\JWT\JWT;
$key = "tfcconsultinggroup";
$token = $_GET["token"];
// try{
// $decoded = JWT::decode($token, $key, array('HS256'));
///

$idempresa = "> 0";
if ($_GET["idempresa"]){
$idempresa = "= " . $_GET["idempresa"];
}


//$sql = "SELECT * FROM empresas emp inner join `opcionesempresa` oemp on emp.id = oemp.idempresa where emp.id ".$idempresa." order by emp.id";
$sql = 'select emp.nombre,oemp.* from'.
'(select idempresa,'.
'MAX(IF(idopcion=1, idopcion, NULL)) AS informes,'.
'MAX(IF(idopcion=2, idopcion, NULL)) AS mantenimientos,'.
'MAX(IF(idopcion=3, idopcion, NULL)) AS limpiezas,'.
'MAX(IF(idopcion=8, idopcion, NULL)) AS planificaciones,'.
'MAX(IF(idopcion=9, idopcion, NULL)) AS incidencias'.
' from opcionesempresa '.
' GROUP BY idempresa ) oemp '.
'RIGHT OUTER JOIN empresas emp '.
'ON oemp.idempresa = emp.id '.
'WHERE oemp.informes =1 AND emp.activa = 1 AND emp.id '.$idempresa.
' ORDER BY emp.nombre;';

$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'","sql":"'.$sql.'"}');


  while ($reg=mysqli_fetch_array($registros))
  { 
    $rows[] = $reg;
  }
  if($registros){
    $result = '{"success":"true","data":' . json_encode($rows) . ',"sql":"'.$sql.'"}';
  }
 


print $result;
//}
//catch (Exception $e) {
//    echo '{"success":"false","token":$token,"error":"',  $e->getMessage(), '"}';
//}

?>