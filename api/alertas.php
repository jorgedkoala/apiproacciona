<?php
header('Access-Control-Allow-Origin: *');  
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
//require 'PHPMailerAutoload.php';
///CONPROBAR TOKEN
///CONPROBAR TOKEN
require_once('./jwt/jwt2.php');
use \Firebase\JWT\JWT;
$key = "tfcconsultinggroup";
$token = $_GET["token"];
try{
$decoded = JWT::decode($token, $key, array('HS256'));
///

$cabecera = "<table width='80%' style='background-color:#ededed'><tr><td style='background-color:#ffffff'>
<img src='https://tfc.proacciona.es/assets/images/logo.jpg'></td></tr>
<tr><td height='35px'></td></tr><tr><td style='background-color:#ffd740' height='35px'><h1>Planificaciones TFC</h1></td></tr><tr><td height='35px'></td></tr><tr><td>";

$pie = "<tr><td  height='35px'></td></tr><tr><td height='35px' style='background-color:#ffd740'><b>Atentamente, el equipo de TFC.</b></td></tr></table>";

$idempresa = $_GET["idempresa"];
//include ("./library.php"); // include the library file
include ("./inc/class.phpmailer.php"); // include the class name
include ("./inc/class.smtp.php");
include("./config/conexion2.php");

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
$email->$emailUser; 
$email->$emailPassword; 
$email->setFrom("alertes@proacciona.es");
$email->addReplyTo('alertes@proacciona.es', 'Proacciona');
$email->FromName  = 'Proacciona';
$email->Subject   = 'Proacciona planificaciones' ;

  // $email->addAddress( 'jorged@ntskoala.com' );
/////******** FIN PREPARE EMAIL ********//
/////******** FIN PREPARE EMAIL ********//
/////******** FIN PREPARE EMAIL ********//

$sql =  "SELECT alertas.*, planificaciones.id, planificaciones.nombre,planificaciones.fecha FROM alertas 
INNER JOIN planificaciones ON planificaciones.idempresa = alertas.idempresa 
WHERE (alertas.modulo='planificaciones' 
AND planificaciones.fecha 
BETWEEN  DATE_ADD(NOW(), INTERVAL alertas.tiempo_alerta -1 DAY)
AND DATE_ADD(NOW(), INTERVAL alertas.tiempo_alerta DAY))
OR
(alertas.modulo='planificaciones' 
AND planificaciones.fecha =
CURDATE() )
order by alertas.idempresa";
$empresaActual=0;
$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'"}');
	while ($reg=mysqli_fetch_array($registros))
	{	
        if ($reg["idempresa"] != $empresaActual){
            echo  date('Y-m-d H:i:s') ." Envío para: ";
        $email->clearAddresses();	
        $users = json_decode($reg["usuarios"]);
        
        $queryUsers = 'id = null';
        foreach ($users as $clave=>$valor)
   		{
        $queryUsers .= ' OR id = ' . $valor;
   		}
        
		$sqlUsuarios = "select email from usuarios where " . $queryUsers;
        
        $emails=mysqli_query($conexion,$sqlUsuarios) or die('{"success":"false","error":"query->'.mysqli_error($conexion).'"}');
        

        while ($regMail=mysqli_fetch_array($emails))
	    {	
            //if ($reg["email"]){
                if (!$email->addAddress($regMail["email"])){
                    echo $regMail["email"] . " No añadido<br>";
                }
            //}
        }
        }
		$empresaActual = $reg["idempresa"];	
        $body =  '<b>Recuerda: '. $reg["nombre"] . ' está programado para el: ' . $reg["fecha"] . "</b>";
        $email->Body      = $cabecera . $body . $pie .$eol;
        var_dump ($email->getToAddresses());
        if (!$email->Send($body)){
             echo "Mailer Error: " . $mail->ErrorInfo ."<BR>";
        }else{
            echo 'Envío ok';
            
        }
	}




}

catch (Exception $e) {
    echo '{"success":"false","error":"',  $e->getMessage(), '"}';
}
?>

