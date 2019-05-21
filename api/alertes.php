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
$getBody = $_GET["body"];
$getURI =  $_GET["uri"];
$remite = $_GET["remite"]; 
$idempresa = $_GET["idempresa"];
$userId = $_GET["userId"];
$resumen=$remite;
$empresa ="";
//include ("./library.php"); // include the library file
include ("./inc/class.phpmailer.php"); // include the class name
include ("./inc/class.smtp.php");
include("./config/conexion2.php");
$eol = PHP_EOL;

if ($remite == 'help'){
$pie = "<tr><td height='35px' style='background-color:#ffd740'><b>".urldecode($getURI)."</b></td></tr></table>";
}else{
$pie = "<tr><td height='35px' style='background-color:#ffd740'><b>".urldecode($getURI)."</b></td></tr></table>";
}

$sql = "select * from empresas where id = " . $idempresa ."";
$registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error().'"}');
	while ($reg=mysqli_fetch_array($registros))
	{		
		$empresa = $reg["nombre"];
	}

	if ($remite == 'help'){
		$cabecera = "<table width='500px' style='background-color:#ededed;text-align: center;'><tr><td style='background-color:#ffffff'>
		<img src='https://tfc.proacciona.es/assets/images/logo.jpg'></td></tr>
		<tr><td  height='35px' style='background-color:#ffd740'><b>Nueva consulta abierta por ".$empresa."</b></td></tr><tr><td style='padding: 25px;'>";
		}else{
	$cabecera = "<table width='500px' style='background-color:#ededed;text-align: center;'><tr><td style='background-color:#ffffff'>
	<img src='https://tfc.proacciona.es/assets/images/logo.jpg'></td></tr>
	<tr><td  height='35px' style='background-color:#ffd740'><b>Nueva incidencia abierta en ".$empresa."</b></td></tr><tr><td style='padding: 25px;'>";
		}	
$body = $cabecera.$getBody.$pie.$eol;


if ($remite == 'help'){
	$sql = "select email from usuarios where idempresa = " . $idempresa . " AND tipouser = 'Gerente' AND id=" .$userId."";
	}else{
	$sql = "select email from usuarios where idempresa = " . $idempresa . " AND tipouser = 'Gerente'";
	}


$email = new PHPMailer();
$usuarios=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query1->'.mysqli_error($conexion).'"}');
$numUsuarios = mysqli_num_rows($usuarios);
while ($user=mysqli_fetch_array($usuarios))
{	
	if (!$email->addAddress($user["email"])){
		$resumen=$resumen.$regempresa["email"] . " No añadido.";
		//echo "#".$regempresa["email"] . " No añadido<br>";
	}
}

if(isset($_FILES['logo']))
{
	 $email->AddAttachment($_FILES['logo']['tmp_name'],
                         $_FILES['logo']['name']);
}



$email->IsSMTP(); // enable SMTP
$email->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only, 0 = nada
$email->SMTPAuth = true; // authentication enabled
//$email->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
//$email->Host = "smtp.1and1.es";
$email->Host = "smtp.gmail.com";
//$email->Port = 465; // or 587
$email->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$email->Port = 587;         
$email->IsHTML(true);
//$email->Username = "jorged@ntskoala.com";
//$email->Password = "yzf600r6&zxr750r7";
//$email->SetFrom("jorged@ntskoala.com");

//$email->Username = "info@urbanpoi.com";
//$email->Password = "informix$";
//$email->SetFrom("info@urbanpoi.com");


$email->Username = "alertes@proacciona.es";
$email->Password = $emailPassword;
$email->setFrom("alertes@proacciona.es");



// $registros=mysqli_query($conexion,$sql) or die('{"success":"false","error":"query->'.mysqli_error().'"}');
// 	while ($reg=mysqli_fetch_array($registros))
// 	{		
// 		if (strlen($reg["email"]) >3 ){
// 		$email->addAddress($reg["email"]);
// 		}
// 	}
$email->addReplyTo('alertes@proacciona.es', 'Proacciona');
$email->FromName  = 'Proacciona';
if ($remite == 'help'){
$email->Subject   = 'Ticket de Consulta ' . $empresa;
}else{
	$email->Subject   = 'Proacciona incidencias';
}
   $email->Body      = $body.$eol;
   $email->addAddress( 'jorged@ntskoala.com' );
   $email->addAddress( 'alertes@proacciona.es');
  // $email->addAddress( 'alertes@proacciona.es' );

  try {
 $email->Send();
 $resumen=$resumen. 'Send parece correcto';
  }
  catch (Exception $e) {
    $resumen=$resumen. 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
}
$result = '{"success":"true","data":"ok","hayFoto":"'.$base64.'","resumen":"'.$resumen.'"}';
print json_encode($result);
}

catch (Exception $e) {
    echo '{"success":"false","error":"',  $e->getMessage(), '"}';
}
?>

