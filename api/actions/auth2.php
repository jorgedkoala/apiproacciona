<?php
require_once('../jwt/jwt2.php');
use \Firebase\JWT\JWT;
$issuedat= time();
$notbefore = time(); //$issuedat + 1;
$expire = $issuedat + 36000;
$role = "admin";
$user = "iduser";

$key = "tfcconsultinggroup";
$token = array(
    "iss" => "http://tfc.", //IDENTIFICADOR DE DOMINIO
    "aud" => "http://tfc.com", //
    "iat" => $issuedat,//1356999524, // Issued at: time when the token was generated
  //  "nbf" => $notbefore,
    "exp" => $expire,
    "rol"=> $role, // Not before
    "jti"=> $user
);

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
$jwt = JWT::encode($token, $key);
echo $jwt . "<br>";
try{
$decoded = JWT::decode($jwt, $key, array('HS256'));
}
catch (Exception $e) {
    echo '{"success":"false","error":"',  $e->getMessage(), '"}';
}
echo "<pre>";
print_r($decoded);
echo "</pre>";
/*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

$decoded_array = (array) $decoded;

/**
 * You can add a leeway to account for when there is a clock skew times between
 * the signing and verifying servers. It is recommended that this leeway should
 * not be bigger than a few minutes.
 *
 * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
 */
JWT::$leeway = 60; // $leeway in seconds
$decoded = JWT::decode($jwt, $key, array('HS256'));

?>