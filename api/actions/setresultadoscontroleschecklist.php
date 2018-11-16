<?php 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
include("../config/conexion2.php");
$method = $_SERVER['REQUEST_METHOD'];
//print $method;
//$resultados = json_decode($_POST["resultados"]);
$request_body = file_get_contents('php://input');
//echo $request_body;
$resultados = json_decode($request_body,true);

$idempresa=$_GET["idempresa"];
$dir= '../../controles/'.$idempresa;
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}
function base64_to_jpeg($base64_string, $output_file, $dire) {
    $ifp = fopen($dire."/".$output_file, "wb"); 

    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp); 

    return $output_file; 
}



$i = 0;
$campoFoto=NULL;
foreach ($resultados[0] as $key => $object) {
    $columns .= ($i>0?',':'(').$key;
	if ($key == 'fotocontrol') $campoFoto = $i;//modificado
    $i +=1;
}
$columns .= ")";
//echo $columns;
$values="VALUES ";
$j =0;
$k =0;
foreach ($resultados as $key => $resultado){
	$values .= ($j>0?'),':''). "(";
	$j +=1;
	$k=0;
	foreach ($resultado as $item){
		error_log("fila" . $j . " Resultado" . $k . " " . $item . "\n", 3, "../../controles/log.log");
		if ($k == 0) $nom = $item;
		if ($k == 1) $nom .= "_" . $item; 
		if ($k == $campoFoto){
		//if ($k == 4){
			$foto = $item;
			$hayfoto = (strlen($item)>0?$nom:"false");
			$values .= ",'".$hayfoto."'";
		} else {
		$values .= ($k>0?',':'')."'".mysqli_real_escape_string($conexion,(string)$item)."'";
		}
		$k +=1;
	}
//}
$values .= ');';
//echo $values;
//$columns = array_keys($resultados);
//$columns = array_map(function ($value) {}, $resultados[0]);
$sql = "INSERT INTO resultadoschecklistcontrol $columns $values";
//echo $sql;
$registros=mysqli_query($conexion,$sql) or die(json_encode('{"success":"false","error":"query->'.mysqli_error($conexion).'"}'));
$result = '{"success":"true","id":"' . mysqli_insert_id($conexion). '"}';
if (strlen($foto)>1){
$image = base64_to_jpeg( $foto, 'checklistcontrol' . $nom. '.jpg', $dir);
}
$values="VALUES ";
$j = 0;

}
print json_encode($result);
?>