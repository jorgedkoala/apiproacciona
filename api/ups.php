<?php 
header('Access-Control-Allow-Origin: *');  
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
use google\appengine\api\cloud_storage\CloudStorageTools;

$options = ['gs_bucket_name' => 'tfc1-181808.appspot.com'];
$upload_url = CloudStorageTools::createUploadUrl('ups_exito.php', $options);

echo 'ok.';
?>