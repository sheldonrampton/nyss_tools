<?php
  /*
   * Requires the XML-RPC for PHP library (current version, 3.0.0beta).
   * http://phpxmlrpc.sourceforge.net/
   * http://sourceforge.net/projects/phpxmlrpc/files/phpxmlrpc/3.0.0beta/xmlrpc-3.0.0.beta.zip/download
  */
  include('xmlrpc/lib/xmlrpc.inc');
  /*
   * To use the New York State Senate API, you must first obtain a
   * domain name and API key by visiting http://www.nysenate.gov/developers
   */
  $domain_name   = '';
  $api_key        = '';

  $endPoint      = 'http://www.nysenate.gov/services/xmlrpc';
  $methodName   =   "views.get";
  $domain_time_stamp = (string)time();
  $code = md5(uniqid(rand(), true));
  $nonce = substr($code, 0, 20);
  $hash = hash_hmac('sha256', $domain_time_stamp .';'.$domain_name .';'. $nonce .';'. $methodName, $api_key);
  $client = new xmlrpc_client($endPoint);
  $key = array(
    new xmlrpcval($hash, "string"), 
    new xmlrpcval($domain_name, "string"),
    new xmlrpcval($domain_time_stamp, "string"),
    new xmlrpcval($nonce, "string"),
    new xmlrpcval(16, "int"),
    new xmlrpcval(array(        // this parameter (optional) specifies the fields to be returned from the node
      new xmlrpcval('nid', "string"),
      new xmlrpcval('title', "string"),
    ), "array"),
  );
  $message = new xmlrpcmsg($methodName, $key);
  $client->return_type = 'xml';            
  $result = $client->send($message);
  $values = xmlrpc_decode($result->value());
  print var_dump($values);
?>