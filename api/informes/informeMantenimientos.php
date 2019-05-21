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
$std=["usuario","maquina","mantenimiento","fecha_prevista","fecha","desdcripcion","causas","tipo","tipo2","pieza","cantidadPiezas"];
foreach ($std as $valor) {
  $columna = new Columna();
  $columna->id=0;
  $columna->nombre=$valor;
 array_push($cols,$columna);
}



//$sql = "SELECT id,nombre,pla FROM controles WHERE controles.idempresa = '" . $idempresa . "' ORDER BY orden, controles.id";

// $sql = "SELECT id,nombre,pla FROM controles WHERE controles.idempresa = '" . $idempresa . "' ORDER BY orden, controles.id";

// $columnas=mysqli_query($conexion,$sql) or die("{'success':false,'error':".mysqli_error($conexion)."}");
// //echo $sql;
// while ($reg=mysqli_fetch_array($columnas))
// {	
// //$cols[] = $reg["nombre"];
//  $columna = new Columna();
//  $columna->id=$reg["id"];
//  $columna->nombre=$reg["nombre"];
// // $columna->pla=$reg["pla"];
// array_push($cols,$columna);
// }



switch ($method) {
  case 'GET':
    // $sql = "select usuarios.usuario, controles.nombre, ResultadosControl.foto, ResultadosControl.fecha, ResultadosControl.resultado, ResultadosControl.id as idr 
    // from ResultadosControl INNER JOIN controles ON controles.id = ResultadosControl.idcontrol LEFT OUTER JOIN usuarios ON ResultadosControl.idusuario = usuarios.id WHERE controles.idempresa=$idempresa AND ResultadosControl.fecha >= '$fechainicio' AND ResultadosControl.fecha <= '$fechafin' ORDER BY ResultadosControl.fecha DESC"; break;
		$sql = "select usr.usuario,maquina, mantenimiento,fecha_prevista,fecha,descripcion,causas,tipo,tipo2,pieza,cantidadPiezas from mantenimientos_realizados mr INNER JOIN usuarios usr ON mr.idusuario = usr.id  WHERE mr.idempresa=$idempresa AND fecha >= '$fechainicio' AND fecha <= '$fechafin' ORDER BY fecha DESC"; break;
	}
	// select usr.usuario,nombre,fecha_prevista,fecha,descripcion,tipo,fecha_supervision,supervision,detalles_supervision, usr2.usuario from limpieza_realizada lr  INNER JOIN usuarios usr ON lr.idusuario = usr.id LEFT OUTER JOIN usuarios usr2 ON lr.idsupervisor = usr2.id WHERE lr.idempresa=2 AND fecha >= '2018-06-01' AND fecha <= '2018-06-31' ORDER BY fecha DESC
$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'"}');

if ($method == 'GET') {
	while ($reg=mysqli_fetch_array($registros))
	{	
		$rows[] = $reg;
	}
	if (is_null($rows)){
		$rows=[];
	}
	$sql2 = "select count(*) as pendientes  FROM maquinaria M inner join maquina_mantenimiento mm on M.id=mm.idmaquina where (mm.fecha <= CURDATE() and M.idempresa = ".$idempresa.")";
	$pendientesM=mysqli_query($conexion,$sql2) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'"}');
	while ($reg=mysqli_fetch_array($pendientesM))
	{	
		$mantenimientosPendientes = $reg["pendientes"];
	}
	$sql2 = "select count(*) as pendientes  FROM maquinaria M inner join maquina_calibraciones mm on M.id=mm.idmaquina where (mm.fecha <= CURDATE() and M.idempresa = ".$idempresa.")";
	$pendientesC=mysqli_query($conexion,$sql2) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'"}');
	while ($reg=mysqli_fetch_array($pendientesC))
	{	
		$calibracionesPendientes = $reg["pendientes"] + mantenimientosPendientes;
	}
	$sql3="select 'Mantenimiento' as tipo,mm.nombre, mm.fecha, mm.periodicidad from maquinaria M inner JOIN maquina_mantenimiento mm ON M.id = mm.idmaquina WHERE M.idempresa = $idempresa UNION
	select 'Calibracion' as tipo,mc.nombre, mc.fecha, mc.periodicidad from maquinaria M inner JOIN maquina_calibraciones mc ON M.id = mc.idmaquina WHERE M.idempresa = ".$idempresa;
	$mm=mysqli_query($conexion,$sql3) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'"}');
	while ($reg=mysqli_fetch_array($mm))
	{	
		$MantenimientosMaquinaria[] = $reg;
	}
	if($registros){
		$result = '{"success":"true","columnas":' . json_encode($cols) . ',"valores":' . json_encode($rows) . ',"pendientes":' . $calibracionesPendientes . ',"mm":' . json_encode($MantenimientosMaquinaria) . '}';
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