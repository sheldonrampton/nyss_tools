<?php
  /*
   * Requires the XML-RPC for PHP library (current version, 3.0.0beta).
   * http://phpxmlrpc.sourceforge.net/
   * http://sourceforge.net/projects/phpxmlrpc/files/phpxmlrpc/3.0.0beta/xmlrpc-3.0.0.beta.zip/download
  */
  include('xmlrpc/lib/xmlrpc.inc');
  $domain_name   = '';
  $api_key        = '';
  $endPoint      = 'http://www.nysenate.gov/services/xmlrpc';
  $methodName   =   "node.get"; 
  $domain_time_stamp = (string)time();
  $code = md5(uniqid(rand(), true));
  $nonce = substr($code, 0, 20);
  $hash = hash_hmac('sha256', $domain_time_stamp .';'.$domain_name .';'. $nonce .';'. $methodName, $api_key);
  /*** client side ***/
  $client = new xmlrpc_client('http://www.nysenate.gov/services/xmlrpc');

  // tell the client to return raw xml as response value
  $client->return_type = 'xml';
  $result = $client->send(xmlrpc_encode_request('node.get', 
    array(
      $hash,
      $domain_name,
      $domain_time_stamp,
      $nonce,
      16,
      array('nid','title'), // this parameter is optional
    )
  ));

  if ($result->faultCode()) {
    // HTTP transport error
    echo 'Got error '.$result->faultCode();
  }
  else {
    // HTTP request OK, but XML returned from server not parsed yet
    $values = xmlrpc_decode($result->value());
    // check if we got a valid xmlrpc response from server
    if ($values === NULL) {
      echo 'Got invalid response';
    }
    else {
      // check if server sent a fault response
      if (xmlrpc_is_fault($values)) {
        echo 'Got xmlrpc fault '.$values['faultCode'];
      }
      else {
        //echo'Got response: '.htmlentities($values);
        echo'<h2>130 Got response:</h2> ';
      }
      echo var_dump($values);
    }
  }
?>