<?php 
header('Access-Control-Allow-Origin: *');  
header("Access-Control-Allow-Methods: GET");
include("../config/conexion2.php");

//require 'PHPMailerAutoload.php';
///CONPROBAR TOKEN
///CONPROBAR TOKEN
require_once('../jwt/jwt2.php');
use \Firebase\JWT\JWT;
$key = "tfcconsultinggroup";
$token = $_GET["token"];


try{
$decoded = JWT::decode($token, $key, array('HS256'));

$entidad = $_GET["entidad"];
$fecha = $_GET["fecha"];
$idholding= $_GET["idholding"];
$holding='';
if($_GET["idholding"]){
$holding="AND (e.id=$idholding OR idholding=".$idholding.")";
}


switch($entidad){
    case "incidencias":
    $sql ="select e.nombre as nombreEmpresa,e.holding as holding,e.idholding as idholding, i.* from empresas e inner join incidencias i  on e.id = i.idempresa where  e.activa= 1 AND i.fecha >= '".$fecha."' ".$holding." order by e.nombre";
        // $sql ="select e.nombre as nombreEmpresa,e.holding as holdingEmpresa,e.idholding as idholdingEmpresa, i.* from empresas e inner join incidencias i  on e.id = i.idempresa where e.activa= 1 AND i.fecha >= '".$fecha."' order by e.nombre";
        break;
    case "controles":
        // $sql ="select e.nombre as nombreEmpresa, i.* from empresas e inner join incidencias i  on e.id = i.idempresa where i.fecha >= '".$fecha."' order by e.nombre";
        break;
    case "logins":
    // $sql ="select e.nombre as nombreEmpresa, l.*, count(*) as total from empresas e inner join logs l  on e.id = l.idempresa where e.activa= 1 AND l.fecha >= '".$fecha."' order by e.nombre";
    $sql ="select e.nombre as nombreEmpresa,e.holding as holding,e.idholding as idholding, l.*, count(*) as total from empresas e inner join logs l  on e.id = l.idempresa where e.activa= 1 AND l.fecha >= '".$fecha."' ".$holding." GROUP BY e.nombre,l.idusuario,l.accion,l.tabla order by e.nombre";

        break;
}


$registros=mysqli_query($conexion,$sql) or die("{'success':false,'error':".mysqli_error($conexion). $sql."}");
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

}catch (Exception $e) {
    echo '{"success":"false","error":"',  $e->getMessage(), '"}';
}

?>