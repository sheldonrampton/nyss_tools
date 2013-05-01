<?php
$api_key = ''; // Your API key.
$domain = '';
$timestamp = (string) time();
$method = 'node.get'; // NEEDS THIS LINE
$code = md5(uniqid(rand(), true));
$nonce = substr($code, 0, 20);
$hash = hash_hmac('sha256', $timestamp .';'.$domain .';'. $nonce .';'.$method, $api_key);

/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, 'http://www.nysenate.gov/services/xmlrpc');

//prepare the field values being posted to the service
$data = array(
  'method' => '"' . $method . '"', // PUT $method HERE, NOT FIXED STRING
  'hash' => '"'. $hash .'"',
  'domain_name' => '"'. $domain .'"',
  'domain_time_stamp' => '"'. $timestamp .'"',
  'nonce' => '"'. $nonce .'"',  
  'api_key' =>'"'. $api_key .'"', 
  'nid' => '"16"', //use 100 on local nysenate.gov
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

//make the request
$result = curl_exec($ch);
print_r($result);
//print '<pre>' . print_r(special_parse_json($result, TRUE), TRUE) . '</pre>';
//print_r('{"data":' . $result . '}');
//print_r(json_decode(str_replace('"#', '"', $result), TRUE));
//print_r(json_decode($result, TRUE));
//print_r($result);
*/

$required_args = array($hash, $domain, $timestamp, $nonce, $nid);

// prepare the request
$request = xmlrpc_encode_request(
  $method, $required_args
);

// prepare the request context
$context = stream_context_create(
  array(
    'http' => array(
      'method' => "POST",
      'header' => "Content-Type: text/xml",
      'content' => $request,
    )
  )
);

// connect
$connect = file_get_contents($endpoint, false, $context);
// retrieve the result
$response = xmlrpc_decode($connect);

// display the result on screen
if (xmlrpc_is_fault($response)) {
    print '<h1>Error</h1>';
    trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
} else {
    // let's look at what came back
    print '<h1>Received</h1>';
    print '<pre>'. htmlspecialchars(print_r($response, true)) .'</pre>';
}

