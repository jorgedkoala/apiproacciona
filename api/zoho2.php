<?php 
header('Access-Control-Allow-Origin: *'); 
echo ('<HR>INICIO:<HR>');
$orgID='20064420683';
//$method = $_SERVER['REQUEST_METHOD'];
//$token=$_GET["token"];
$token='1000.a295be9e5875b1cc1556e9b3abee7bfd.3c2f96a81b6d0b9e3f2a3695bcbd7c40';
$input = file_get_contents('php://input');

//$method = 'POST';


function callAPI($method, $url, $data, $headers){
    $curl = curl_init();
 
    switch ($method){
       case "POST":
       echo 'posting: '.$url.'<BR>';
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
          echo 'posting data: '.$data.'<BR>';
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

    echo ('<HR>token3:'.$token.'<HR>');
    


    //var_dump ($headers);

    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    if ($headers)
    curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //echo ();

    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 }


function refreshToken(){
    echo ('<HR>INICIO 2:<HR>');
    echo ('<HR>token2:'.$token.'<HR>');
 $url='https://accounts.zoho.eu/oauth/v2/token';
 $data=array(
 'refresh_token'=>'1000.2044e3a03f9e139cff485e1ca3e24afb.fa393af12db0517238f6452aec6d3218',
 'client_id'=>'1000.IOF6NI3XTKFA914737J57V48MJ1EVA',
 'client_secret'=>'5d00441b2484810e2e0229bfa73a20b9b466a0debe',
 //'redirect_uri'=>'https://tfc.proacciona.es/api/zoho.php',
 'scope'=>'Desk.tickets.READ,Desk.tickets.CREATE,Desk.basic.READ',
 'grant_type'=>'refresh_token');
 $url = sprintf("%s?%s", $url, http_build_query($data));
 $get_data1 = callAPI('POST', $url, null,null);
 echo ('RESULTADO1'.$get_data1.'<BR>');
 $response = json_decode(utf8_encode($get_data1),true);

 //echo ('ACCESS TOKEN1:'. $response["access_token"].'<BR>');
 $token=$response["access_token"];
 newTicket($token);

}

function newTicket($token){
    echo ('<HR>token4:'.$token.'<HR>');
 $ticket= array(
    'subject'=>'asunto API5',
    'departmentId'=>'16266000000007061',
    'contactId'=>'16266000000041242'
);
$data= json_encode($ticket);  
$headers =  array(
    'Authorization:Bearer ' . $token .'',
    'orgId:20064420683',
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($data)
);
 
$url='https://desk.zoho.eu/api/v1/tickets';
//$data=$input;
//echo ('before Call'.$url.'<BR>'.$data.'<BR>');
$get_data2 = callAPI('POST', $url, $data,$headers);
echo ('<HR>'.$get_data2);
}

refreshToken();

//newTicket($token);





?>