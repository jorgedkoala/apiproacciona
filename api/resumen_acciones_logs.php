<?php 
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT"); 
include("./config/conexion2.php");
///CONPROBAR TOKEN
///CONPROBAR TOKEN
require_once('./jwt/jwt2.php');
use \Firebase\JWT\JWT;
$key = "tfcconsultinggroup";
$token = $_GET["token"];
try{
$decoded = JWT::decode($token, $key, array('HS256'));
///

$method = $_SERVER['REQUEST_METHOD'];
$idempresa = $_GET["idempresa"];
$fechainicio = $_GET["fechainicio"];
$key = $_GET["id"];

$input = json_decode(file_get_contents('php://input'),true);

 
// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    $sql = "SELECT count(*) as total,l.idusuario, l.fecha, l.tabla,l.accion,l.plataforma FROM 
	logs l
WHERE
	l.idempresa = $idempresa
AND
	l.fecha > $fechainicio
    AND (l.tabla = 'ResultadosControl'
         OR l.tabla = 'resultadoschecklist'
          OR l.tabla = 'limpieza_realizada'
          OR l.tabla = 'mantenimientos_realizados'
        	OR l.tabla = 'incidencias'
        OR l.tabla = 'login')
          
GROUP BY l.idusuario, l.tabla,l.accion,l.plataforma"; break;
}
//echo $sql;
$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error($conexion).$sql.'"}');

if ($method == 'GET') {
	while ($reg=mysqli_fetch_array($registros))
	{	
		$rows[] = $reg;
	}
	if($registros){
		//$result = '{"success":"true","data":' . json_encode($rows) . ',"sql":"' . $sql . '"}';
		$result = '{"success":"true","data":' . json_encode($rows) .  '}';
	}
	else {
		$result = '{"success":"false"}';
	}
}

print json_encode($result);

}
catch (Exception $e) {
    echo '{"success":"false","error":"',  $e->getMessage(), '"}';
}

?>