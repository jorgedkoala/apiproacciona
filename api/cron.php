<?php


$url="http://tfc.ntskoala.com/api/actions/login.php?user=demo&password=demo";  
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            //curl_setopt($ch, CURLOPT_HEADER, TRUE); 
            //curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            $content = curl_exec($ch); 
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            curl_close($ch); 

$contenido = json_decode($content);
$cont =  json_decode($contenido);
// echo "1" . $cont["token"];

$resultado = file_get_contents("http://tfc.ntskoala.com/api/alertas.php?token=".$cont->token);
//$resultado = file_get_contents("http://tfc.ntskoala.com/api/alertascontroles.php?token=".$cont->token);
echo $resultado;
//echo "tarea programada:".$resultado.'\n';


?>