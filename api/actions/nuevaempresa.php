<?php 
header('Access-Control-Allow-Origin: *');  
include("../config/conexion2.php");
 
$empresa = $_POST["nombre"];
$password = $_GET["password"];
$token = strrev( str_replace(".","",$_SERVER['SERVER_ADDR']) );
if ($token <> $_POST["token"])
{
	$result = '{"success":"false","error":"Error con el token" ,"data":""}';
print json_encode($result);
}
else
{
$registros=mysqli_query($conexion,"select * from empresas where nombreempresa = '" . $empresa . "'") or die('{"success":"false","error":"'.mysqli_error($conexion).'"}');
$numero_filas = mysqli_num_rows($registros);
if($numero_filas){
$result = '{"success":"false","error":"La empresa ya existe" ,"data":""}';
}
else {
$registros=mysqli_query($conexion,"INSERT INTO empresas SET nombreempresa = '" . $empresa . "'") or die('{"success":"false","error":"'.mysqli_error($conexion).'"}');
$result = '{"success":"true","data":""}';
}
print json_encode($result);
}
?>