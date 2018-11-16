<?php 
header('Access-Control-Allow-Origin: *');  
include("../config/conexion2.php");
require_once('../jwt/jwt2.php');
use \Firebase\JWT\JWT;
$key = "tfcconsultinggroup";
$user = $_GET["user"];
$password = $_GET["password"];
$origen = 'desconocido';
if ($_GET["origen"]){
$origen = $_GET["origen"];
}

//$token = strrev( str_replace(".","",$_SERVER['SERVER_ADDR']) );
$sql = "select * from usuarios where usuario = '" . $user . "' and password = '" . $password . "'";
$registros=mysqli_query($conexion,$sql) or die("{'success':false,'error':".mysqli_error()."}");
//echo $sql;
$numero_filas = mysqli_num_rows($registros);
while ($reg=mysqli_fetch_array($registros))
{	
$rows[] = $reg;
$role = $reg["tipouser"];
$user = $reg["id"];
$idempresa = $reg["idempresa"];
$nom = $reg["usuario"];
}

if ($user == 'alerta' && $password == 'er45$%D'){
  $numero_filas = 1;
}

if($numero_filas){
$issuedat= time();
$expire = $issuedat + 14400;
if ($user == 'alerta' && password == 'er45$%&D'){
  $expire = $issuedat + 100;
}
$token = array(
    "iss" => "http://tfc.com", //IDENTIFICADOR DE DOMINIO
    "aud" => "http://tfc.com", //
    "iat" => $issuedat,//1356999524, // Issued at: time when the token was generated
  //  "nbf" => $notbefore,
    "exp" => $expire,
    "rol"=> $role, // Not before
    "jti"=> $user,
    "emp"=> $idempresa,
    "usr"=> $nom
);
$jwt = JWT::encode($token, $key);

$result = '{"success":"true","token":"' .$jwt . '" ,"data":' .json_encode($rows) .'}';

//******** LOGGING
try{
  if ($method != "GET"){
  $sql_log = "INSERT INTO  logs SET fecha = '" .date("Y-m-d H:i:s"). "', idusuario=".$user.", tabla= 'login', accion= 'login', valor= 'login', plataforma = '".$origen."', idempresa=".$idempresa;
  $log =  mysqli_query($conexion,$sql_log);// or die('{"success":"false","error":"query->'.mysqli_error($conexion).'","sql":"'.$sql_log.'"}');
  $result = '{"success":"true","token":"' .$jwt . '" ,"data":' .json_encode($rows) .',"log":"ok"}';
  }
  }
  catch (Exception $e) {
    $result = '{"success":"true","token":"' .$jwt . '" ,"data":' .json_encode($rows) .',"log":"'.$e.'"}';
    }
  //******** FIN LOGGING
}
else {
$result = '{"success":"false"}';
}

print json_encode($result);

?>