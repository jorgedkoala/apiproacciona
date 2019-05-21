<?php
header('Access-Control-Allow-Origin: *');  
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
//require 'PHPMailerAutoload.php';
///CONPROBAR TOKEN
///CONPROBAR TOKEN
require_once('../jwt/jwt2.php');
use \Firebase\JWT\JWT;
$key = "tfcconsultinggroup";
$token = $_GET["token"];
// try{
// $decoded = JWT::decode($token, $key, array('HS256'));
///

$cabecera = "<table width='80%' style='background-color:#ededed'><tr><td style='background-color:#ffffff'>
<img src='https://tfc.proacciona.es/assets/images/logo.jpg'></td></tr>
<tr><td height='35px'></td></tr><tr><td style='background-color:#ffd740' height='35px'><h1>Informes TFC</h1></td></tr><tr><td height='35px'></td></tr><tr><td>";

$pie = "<tr><td  height='35px'></td></tr><tr><td height='35px' style='background-color:#ffd740'><b>Atentamente, el equipo de TFC.</b></td></tr></table>";
$titulo1 = "<tr><td  height='35px'></td></tr><tr><td height='35px' style='background-color:#ffd740'><b>";


$titulo2 = "</b></td></tr>";

$colores = array("#cccccc","#335ccc","#cc4a33","#33cc33");
$idempresa = $_GET["idempresa"];
//include ("./library.php"); // include the library file
include ("../inc/class.phpmailer.php"); // include the class name
include ("../inc/class.smtp.php");
include("../config/conexion2.php");

//$sql = "select * from alertas where idempresa = " . $idempresa . " AND tipouser = 'Gerente'";
$mail = array();


/////******** PREPARE EMAIL ********//
/////******** PREPARE EMAIL ********//
/////******** PREPARE EMAIL ********//
$email = new PHPMailer();
$email->IsSMTP(); // enable SMTP
$email->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
$email->SMTPAuth = true; // authentication enabled
//$email->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
//$email->Host = "smtp.1and1.es";
$email->Host = "smtp.gmail.com";
//$email->Port = 465; // or 587
$email->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$email->Port = 587;         
$email->IsHTML(true);
$email->Username = "alertes@proacciona.es";
$email->Password = $emailPassword;
$email->setFrom("alertes@proacciona.es");
$email->addReplyTo('alertes@proacciona.es', 'Proacciona');
$email->FromName  = 'Proacciona';
$email->Subject   = 'Proacciona alertas' ;
$email->addAddress( 'jorged@ntskoala.com' );
/////******** FIN PREPARE EMAIL ********//
/////******** FIN PREPARE EMAIL ********//
$empresaActual = 0;
$body= '<b>Recuerda controles programados para hoy: <BR>';
$empresasSQL = "SELECT * FROM `usuarios` WHERE (`tipouser` = 'Gerente' or `tipouser` = 'Mantenimiento') and `email` != '' and idempresa = 2  order by `idempresa`";
$empresas=mysqli_query($conexion,$empresasSQL) or die('{"success":"false","error":"query1->'.mysqli_error($conexion).'"}');
$numEmpresas = mysqli_num_rows($empresas);
while ($regempresa=mysqli_fetch_array($empresas))
{
/////******** INICIO BUCLE EMPRESAS ********//	
if ($regempresa["idempresa"] != $empresaActual){
    
$sqlControles = array(
    "SELECT i.incidencia as nombre, i.* from incidencias i WHERE (`fecha` BETWEEN  DATE_ADD(NOW(), INTERVAL -7 DAY)
    AND DATE_ADD(NOW(), INTERVAL 0 DAY) AND `idempresa` = ".$regempresa['idempresa'].")",
    "SELECT ctl.nombre, ctl.fecha_ as fecha FROM `controles` ctl WHERE (`fecha_` < CURDATE() AND `idempresa` = ".$regempresa['idempresa'].")",
    "SELECT cl.nombrechecklist as nombre , cl.fecha_ as fecha  FROM `checklist` cl WHERE (`fecha_` < CURDATE() AND `idempresa` =".$regempresa['idempresa'].")",
    "SELECT LZ.nombre,LZ.id , LE.* FROM limpieza_zona LZ inner join limpieza_elemento LE on LZ.id=LE.idlimpiezazona where (LE.fecha < CURDATE() and LZ.idempresa = ".$regempresa['idempresa'].")",
    "SELECT M.nombre as maquina,M.id , mm.* FROM maquinaria M inner join maquina_mantenimiento mm on M.id=mm.idmaquina where (mm.fecha < CURDATE() and M.idempresa = ".$regempresa['idempresa'].")",
    "SELECT M.nombre as maquina,M.id , mm.* FROM maquinaria M inner join maquina_calibraciones mm on M.id=mm.idmaquina where (mm.fecha < CURDATE() and M.idempresa = ".$regempresa['idempresa'].")"
);
$arrlength = count($sqlControles);
for($x = 0; $x < $arrlength; $x++) {
   
$controles = mysqli_query($conexion,$sqlControles[$x]) or die('{"success":"false","error":"query2->'.mysqli_error($conexion).$sqlControles[$x].'"}');
switch($x){
    case 0:
    $body = $body . $titulo1 . 'Incidencias'.$titulo2;
    break;
    case 1:
    $body = $body . $titulo1 . 'Controles'.$titulo2;
    break;
    case 2:
    $body = $body . $titulo1 . 'Checklists'.$titulo2;
    break;
    case 3:
    $body = $body . $titulo1 . 'Limpiezas'.$titulo2;
    break;
    case 4:
    $body = $body . $titulo1 . 'Mantenimientos maquinaria'.$titulo2;
    break;
    case 5:
    $body = $body . $titulo1 . 'Calibraciones maquinaria'.$titulo2;
    break;
    default:
    break;
}
/////******** INICIO BUCLE CONTROLES ********//
while ($regControl=mysqli_fetch_array($controles))
{
    switch($x){
        case 0:
        $body = $body . '<b>' . $regControl["nombre"] . ': </b> '.$regControl["fecha"].' ' . $regControl["estado"];
        $body = $body . "<a href='https://tfc.proacciona.es/empresas/" . $regControl["idempresa"] . "/"  . $regControl["origenasociado"] . "/"  . $regControl["idOrigenasociado"] . "/"  . $regControl["idOrigen"]. "'><font color='". $colores[$regControl["estado"]+1]. "'>ir a la incidencia</font></a><BR>";
        break;
        case 1:
        $body = $body . '<b>' . $regControl["nombre"] . ': </b> '.$regControl["fecha"].'.<BR>';
        break;
        case 2:
        $body = $body . '<b>' . $regControl["nombre"] . ': </b> '.$regControl["fecha"].'.<BR>';
        break;
        case 3:
        $body = $body . '<b>' . $regControl["nombre"] . ': </b> '.$regControl["fecha"].'.<BR>';
        break;
        case 4:
        $body = $body . '<b>' . $regControl["nombre"] . ': </b> '.$regControl["fecha"].'.<BR>';
        break;
        case 5:
        $body = $body . '<b>' . $regControl["nombre"] . ': </b> '.$regControl["fecha"].'.<BR>';
        break;
        default:
        $body = $body . '<b>' . $regControl["nombre"] . ': </b> '.$regControl["fecha"].'.<BR>';
        break;
    }
}
}
}
// if (!$email->addAddress($regempresa["email"])){
//     // echo "#".$regempresa["email"] . " No añadido<br>";
// }
$numEmpresas--;

if (($empresaActual > 0 && $regempresa["idempresa"] != $empresaActual) || $numEmpresas==0){
    $email->Body      = $cabecera . $body . $pie .$eol;
    //var_dump ($email->getToAddresses());
    if (!$email->Send()){
         echo "Mailer Error: " . $mail->ErrorInfo ."<BR>";
    }else{
        echo 'Envío ok';
    }
    $body= '<b>Recuerda: <BR>';
    $email->clearAddresses();
}

$empresaActual = $regempresa["idempresa"];



}
/////******** FIN BUCLE EMPRESAS ********//
// }
// catch (Exception $e) {
//     echo '{"success":"false","error":"',  $e->getMessage(), '"}';
// }
?>
