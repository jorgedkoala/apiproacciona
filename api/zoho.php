<?php 
header('Access-Control-Allow-Origin: *'); 

$code = $_GET["code"];
$method = $_SERVER['REQUEST_METHOD'];
$token=$_GET["code"];;
echo ('start<br>');
echo ($_GET.'<br>');
echo ($code.$method.'<br>');

function callAPI($method, $url, $data){
    $curl = curl_init();
 
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
 
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    //    'Authorization:Bearer 1000.e51de33dad1287a4fdc844bd771765e0.7e38c223c460cef087693373a5801069',
    //    'orgId:20064420683',
    //   'Content-Type:application/json'
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    echo ($url);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 }


if($_GET["code"]){
    echo ('hayCode<br>');
    $url='https://accounts.zoho.eu/oauth/v2/token';
    $data=array('code'=>$code,
    'grant_type'=>'authorization_code',
    'client_id'=>'1000.IOF6NI3XTKFA914737J57V48MJ1EVA',
    'client_secret'=>'5d00441b2484810e2e0229bfa73a20b9b466a0debe',
    'redirect_uri'=>'https://tfc.proacciona.es/api/zoho.php',
    'scope'=>'Desk.tickets.READ,Desk.tickets.CREATE,Desk.basic.READ',
    'state'=>'1');
    echo ($url);
    $get_data = callAPI('POST', $url, $data);
}else{
    echo ('no hay code<br>');
//$url="https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id=1000.IOF6NI3XTKFA914737J57V48MJ1EVA&scope=Desk.tickets.READ,Desk.tickets.CREATE,Desk.basic.READ&redirect_uri=https://tfc.proacciona.es/api/zoho.php&state=1&access_type=offline";
}
if($_GET["modo"]=="refreshToken"){
    $url='https://accounts.zoho.eu/oauth/v2/token';
    $data=array(
    'refresh_token='=>'1000.2044e3a03f9e139cff485e1ca3e24afb.fa393af12db0517238f6452aec6d3218',
    'client_id'=>'1000.IOF6NI3XTKFA914737J57V48MJ1EVA',
    'client_secret'=>'5d00441b2484810e2e0229bfa73a20b9b466a0debe',
    'redirect_uri'=>'https://tfc.proacciona.es/api/zoho.php',
    'scope'=>'Desk.tickets.READ,Desk.tickets.CREATE,Desk.basic.READ',
    '&grant_type'=>'refresh_token');

    echo ($url);
    $get_data = callAPI('POST', $url, $data);
}


$response = json_decode($get_data, true);
$errors = $response['response']['errors'];
$data = $response['response']['data'][0];

echo ('INICIO<BR>ERRORES:'.$errors.'<BR>DATA'.$data.'<BR>FIN<HR>');

echo ($get_data);
?>