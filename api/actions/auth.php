<?php
require_once('../jwt/jwt2.php');
$eltoken = $_POST['token'];
$token = array();
$token['id'] = '123456';
$token['email'] = 'jorged@ntskoala.com';
$token['tipouser'] = 'admin';
$mitoken = JWT::encode($token, 'secret_server_key_full_text');
echo $mitoken;
echo "<br>";
echo "";
$token = JWT::decode($mitoken, 'secret_server_key_full_text');
echo $token->id;
echo $token->email;
echo $token->tipouser;
?>