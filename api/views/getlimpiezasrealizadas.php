<?php 
header('Access-Control-Allow-Origin: *');  
include("../config/conexion2.php");

$idempresa = $_GET["idempresa"];
$currentElement =0;
$maxId=0;
//$sql = "SELECT max(lr.id) as maxId,lr.*, le.supervisor, lz.nombre as nombreZona FROM limpieza_realizada lr INNER JOIN limpieza_elemento le ON lr.idelemento = le.id INNER JOIN limpieza_zona lz ON lz.id = le.idlimpiezazona WHERE le.supervisor >0 AND lr.supervision = 0 AND lr.idempresa = '" . $idempresa . "' AND (lr.fecha > (CURDATE() - INTERVAL 3 DAY)) GROUP BY lr.idelemento";
$sql = "SELECT lr.*, le.supervisor, lz.nombre as nombreZona FROM (select max(id) as maxId, mlr.* FROM limpieza_realizada mlr WHERE idempresa = '" . $idempresa . "' AND fecha > (CURDATE() - INTERVAL 3 DAY) GROUP BY idelemento, supervision) lr INNER JOIN limpieza_elemento le ON lr.idelemento = le.id INNER JOIN limpieza_zona lz ON lz.id = le.idlimpiezazona WHERE le.supervisor >0 ORDER BY idelemento, supervision DESC";

$registros=mysqli_query($conexion,$sql) or die("{'success':false,'error':".mysqli_error($conexion)."}");
//echo $sql;
while ($reg=mysqli_fetch_array($registros))
{
    if ($reg["idelemento"] != $currentElement){
        $currentElement = $reg["idelemento"];
        if ($reg["supervision"] == 0){
            $rows[] = $reg;
        }else{
            $maxId=$reg["maxId"]; 
        }
    }else{
        if ($reg["supervision"] == 0 && $reg["maxId"]>$maxId){
            $rows[] = $reg;
        }
    }


}

if($registros){
$result = '{"success":"true","data":' . json_encode($rows) . '}';
}
else {
$result = '{"success":"false"}';
}
print json_encode($result);

?>


