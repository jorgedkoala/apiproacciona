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
$idusuario = $_GET["idusuario"];
$idempresa = $_GET["idempresa"];
$userId = $_GET["userId"];
$key = $_GET["id"];
// get the HTTP method, path and body of the request
//$method = $_SERVER['REQUEST_METHOD'];
//$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
//echo "input:" .$input;
//echo "get: " . $_GET["valores"];
//echo "post: " . $_POST["valores"];
    //$input = json_decode($_POST["valores"],true);
//var_dump(array_values($input));
// retrieve the table and key from the path
//$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
//$key = array_shift($request)+0;
$table = "permissionlimpieza";



// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($conexion) {
  if ($value===null) return null;
  return mysqli_real_escape_string($conexion,(string)$value);
},array_values($input));
// echo "valores:"; var_dump($values);
// build the SET part of the SQL command
$set = '';
for ($i=0;$i<count($columns);$i++) {
  $set.=($i>0?',':'').'`'.$columns[$i].'`=';
  $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
}
 
// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    $sql = "select * from `$table` WHERE idusuario=$idusuario"; break;
  case 'PUT':
    $sql = "update `$table` set $set where id=$key"; break;
  case 'POST':
    $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete from `$table` where id=$key"; break;
}
//echo $sql;
$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error().'"}');

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
} elseif ($method == 'POST') {
  	$result = '{"success":"true","id":' . mysqli_insert_id($conexion). '}';
} else {
  	$result = '{"success":"true","rows":' . mysqli_affected_rows($conexion). '}';
}


//******************ControlGESTION VERSION
try{
if ($method != 'GET'){
$update_empresa = "update empresas set updatecontrols = updatecontrols +1 WHERE id=$idempresa";
$updated=  mysqli_query($conexion,$update_empresa);// or die('{"success":"false","error":"query->'.mysqli_error($conexion).$update_empresa.'"}');
}
}
catch (Exception $e) {
 // echo '{"success":"false","error logging":"',  $e->getMessage(), '"}';
  }
//******************FIN GESTION VERSION

//******** LOGGING
try{
if ($method != "GET"){
$sql_log = "INSERT INTO  logs SET fecha = '" .date("Y-m-d H:i:s"). "', idusuario=".$userId.", tabla= '".$table."', accion= '".$method."', valor= '".mysqli_real_escape_string($conexion,$sql)."', idempresa=".$idempresa;
$log =  mysqli_query($conexion,$sql_log);// or die('{"success":"false","error":"query->'.$sql_log.'"}');
//$result .='{"log":'.$sql_log.'}';
}
}
catch (Exception $e) {
  //echo '{"success":"false","error logging":"',  $e->getMessage(), '"}';
  }
//******** FIN LOGGING


print json_encode($result);

}
catch (Exception $e) {
    echo '{"success":"false","error":"',  $e->getMessage(), '"}';
}

?>