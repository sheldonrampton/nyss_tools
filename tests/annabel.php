<?php

  $api_key = '55f7ea8c500758c8a9230fb9121abc36'; // Your API key.
  $domain = 'nysenate.gov';
  $timestamp = (string) time();
  $nonce = 'xxxxxx'; // my password to login???
  $hash = hash_hmac('sha256', $timestamp .';'.$domain .';'. $nonce .';'. $method, $api_key); // not sure about needing this one?????
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_URL, 'http://www.nysenate.gov/services/json');
 
  //prepare the field values being posted to the service
  $data = array(
    'method' => '"node.get"', 
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
 
?>
