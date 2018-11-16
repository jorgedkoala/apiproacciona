<?php 
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Methods: GET"); 
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




$method = $_SERVER['REQUEST_METHOD'];
$idempresa = $_GET["idempresa"];
$fechainicio = $_GET["fechainicio"];
$fechafin = $_GET["fechafin"] ." 23:59:59";

class Columna
{
    public $id;
    public $nombre;
}
$cols=[];
$std=["usuario","foto","hora","fecha"];
foreach ($std as $valor) {
  $columna = new Columna();
  $columna->id=0;
  $columna->nombre=$valor;
 array_push($cols,$columna);
}



$sql = "SELECT id,nombre,pla FROM controles WHERE controles.idempresa = '" . $idempresa . "' ORDER BY orden, controles.id";
$columnas=mysqli_query($conexion,$sql) or die("{'success':false,'error':".mysqli_error($conexion)."}");
//echo $sql;
while ($reg=mysqli_fetch_array($columnas))
{	
//$cols[] = $reg["nombre"];
 $columna = new Columna();
 $columna->id=$reg["id"];
 $columna->nombre=$reg["nombre"];
// $columna->pla=$reg["pla"];
array_push($cols,$columna);
}



switch ($method) {
  case 'GET':
    $sql = "select usuarios.usuario, controles.nombre, ResultadosControl.foto, ResultadosControl.fecha, ResultadosControl.resultado, ResultadosControl.id as idr 
    from ResultadosControl INNER JOIN controles ON controles.id = ResultadosControl.idcontrol LEFT OUTER JOIN usuarios ON ResultadosControl.idusuario = usuarios.id WHERE controles.idempresa=$idempresa AND ResultadosControl.fecha >= '$fechainicio' AND ResultadosControl.fecha <= '$fechafin' ORDER BY ResultadosControl.fecha DESC"; break;
}

$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error().'"}');

if ($method == 'GET') {
	while ($reg=mysqli_fetch_array($registros))
	{	
		$rows[] = $reg;
	}
	if($registros){
		$result = '{"success":"true","columnas":' . json_encode($cols) . ',"valores":' . json_encode($rows) . '}';
	}
	else {
		$result = '{"success":"false"}';
	}
}

print $result;

// }
// catch (Exception $e) {
//     echo '{"success":"false","error":"',  $e->getMessage(), '"}';
// }

?>