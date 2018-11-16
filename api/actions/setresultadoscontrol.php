<?php 
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT");
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

//echo "<pre>";
//print_r($resultados[0]);
//echo "</pre>";
//$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($resultados));
$i = 0;
$columns = "";
$campoFoto;
foreach ($resultados[0] as $key => $object) {
    $columns .= ($i>0?',':'(').$key;
	if ($key == 'foto') $campoFoto = $i;//modificado
    $i +=1;
}
$columns .= ")";
//echo $columns;
$values="VALUES ";
$j =0;
$k =0;
foreach ($resultados as $key => $resultado){
	$values .= ($j>0?',':''). "(";
	$j +=1;
	$k=0;
	foreach ($resultado as $item){
		if ($k == $campoFoto){
		//if ($k == 3){
			$foto = $item;
			$hayfoto = (strlen($item)>0?'true':'false');
			$values .= ",'".$hayfoto."'";
		} else {
		$values .= ($k>0?',':'')."'".mysqli_real_escape_string($conexion,(string)$item)."'";
		}
		$k +=1;	
	}
$values .= ')';

$sql = "INSERT INTO ResultadosControl $columns $values";
//echo $sql;
$registros=mysqli_query($conexion,$sql) or die(json_encode('{"success":"false","error":"query->'.$sql.mysqli_error().'"}'));
$result = '{"success":"true","id":"' . mysqli_insert_id($conexion). '"}';
if (strlen($foto)>1){
$image = base64_to_jpeg( $foto, 'control' . mysqli_insert_id($conexion). '.jpg', $dir);
}
$values="VALUES ";
$j = 0;
}

print json_encode($result);
?>