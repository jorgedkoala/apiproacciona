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


class Resultado
{
    public $usuario;
    public $foto;
    public $fecha;
    public $idItem;
    public $resultado;
}
class ItemChecklist
{
    public $nombre;
    public $id;
}
$method = $_SERVER['REQUEST_METHOD'];
$idempresa = $_GET["idempresa"];
$fechainicio = $_GET["fechainicio"];
$fechafin = $_GET["fechafin"] ." 23:59:59";

$checklists = [];
$colsChecklists=[];
$resultadosChecklists=[];
$sqlChecks = "SELECT id,nombrechecklist FROM checklist WHERE idempresa = '" . $idempresa . "' ORDER BY orden, id";
$checks=mysqli_query($conexion,$sqlChecks) or die("{'success':false,'error':".mysqli_error($conexion)."}");
//echo $sql;
while ($mischecks=mysqli_fetch_array($checks))
{	
//    $checklists = $reg;
array_push($checklists,$mischecks["nombrechecklist"]);

//*********CARGA LA LISTA DE ITEMS DEL CHECKLIST EN CURSO */
$cols=["usuario","foto","hora","fecha"];
$sqlItemsChecks = "SELECT id,nombre FROM controlchecklist WHERE idChecklist = '" . $mischecks["id"] . "' ORDER BY orden, id";
$itemsChecks=mysqli_query($conexion,$sqlItemsChecks) or die("{'success':false,'error':".mysqli_error($conexion)."}");
//echo $sql;
$itemsChecklist=[];
while ($misItemsCheck=mysqli_fetch_array($itemsChecks))
{
$item = new ItemChecklist();
$item->id=$misItemsCheck["id"];
$item->nombre=$misItemsCheck["nombre"];
array_push($itemsChecklist,$item);
//array_push($cols,$misItemsCheck["nombre"]);
}
array_push($colsChecklists,$itemsChecklist);

//*********CARGA LOS RESULTADOS DE LOS CHECKLISTS EN CURSO */
$cols=["usuario","foto","hora","fecha"];
//$sqlresultadosChecklists = "select *, resultadoschecklist.id as idr, resultadoschecklistcontrol.id as idrc from resultadoschecklist INNER JOIN checklist ON checklist.id = resultadoschecklist.idchecklist INNER JOIN resultadoschecklistcontrol ON resultadoschecklistcontrol.idresultadochecklist = resultadoschecklist.id LEFT OUTER JOIN usuarios on resultadoschecklist.idusuario = usuarios.id WHERE checklist.id=".$mischecks["id"]." AND resultadoschecklist.fecha >= '$fechainicio' AND resultadoschecklist.fecha <= '$fechafin' ORDER BY idr DESC,resultadoschecklistcontrol.idcontrolchecklist DESC, resultadoschecklist.fecha DESC";
$sqlresultadosChecklists = "select *, resultadoschecklist.id as idr, resultadoschecklistcontrol.id as idrc from resultadoschecklist  INNER JOIN resultadoschecklistcontrol ON resultadoschecklistcontrol.idresultadochecklist = resultadoschecklist.id LEFT OUTER JOIN usuarios on resultadoschecklist.idusuario = usuarios.id WHERE resultadoschecklist.idchecklist=".$mischecks["id"]." AND resultadoschecklist.fecha >= '$fechainicio' AND resultadoschecklist.fecha <= '$fechafin' ORDER BY idr DESC,resultadoschecklistcontrol.idcontrolchecklist DESC, resultadoschecklist.fecha DESC";
$resultadosChecklistsQuery=mysqli_query($conexion,$sqlresultadosChecklists) or die("{'success':false,'error':".mysqli_error($conexion)."}");
//echo $sql;
$resultados=[];
while ($misresultadosChecklists=mysqli_fetch_array($resultadosChecklistsQuery))
{
    //$resultados[] = $misresultadosChecklists;
    $miResultado = new Resultado();
    $miResultado->usuario=$misresultadosChecklists["usuario"];
    $miResultado->foto=$misresultadosChecklists["foto"];
    $miResultado->fecha=$misresultadosChecklists["fecha"];
    $miResultado->idItem=$misresultadosChecklists["idcontrolchecklist"];
    $miResultado->resultado=$misresultadosChecklists["resultado"];
    array_push($resultados,$miResultado);
}
array_push($resultadosChecklists,$resultados);
//******** SIGUIENTE CHECKLIST -- FIN while ($mischecks)*/
}


		$result = '{"success":"true","checklists":'.json_encode($checklists).',"columnas":' . json_encode($colsChecklists) . ',"valores":' . json_encode($resultadosChecklists) . '}';



print $result;

// }
// catch (Exception $e) {
//     echo '{"success":"false","error":"',  $e->getMessage(), '"}';
// }

?>