<?php
/**
 * Requires the XML-RPC for PHP library (current version, 3.0.0beta).
 * http://phpxmlrpc.sourceforge.net/
 * http://sourceforge.net/projects/phpxmlrpc/files/phpxmlrpc/3.0.0beta/xmlrpc-3.0.0.beta.zip/download
 */
  include('xmlrpc/lib/xmlrpc.inc');
/**
 * To use the New York State Senate API, you must first obtain a
 * domain name and API key by visiting http://www.nysenate.gov/developers
 */
  $domain_name   = '';
  $api_key        = '';

  // The following lines of code are the same for any node.get method
  $endPoint      = 'http://www.nysenate.gov/services/xmlrpc';
  $methodName   =   "node.get";
  $domain_time_stamp = (string)time();
  $code = md5(uniqid(rand(), true));
  $nonce = substr($code, 0, 20);
  $hash = hash_hmac('sha256', $domain_time_stamp .';'.$domain_name .';'. $nonce .';'. $methodName, $api_key);
  $client = new xmlrpc_client($endPoint);

/**
 *  Parameters for the node.get method:
 *  @param hash (required string)
 *    A valid API key.
 *  @param domain_name (required string)
 *    A valid domain for the API key.
 *  @param domain_time_stamp (required string)
 *    Time stamp used to hash key.
 *  @param nonce (required string)
 *    One time use nonce also used hash key.
 *  @param nid (required int)
 *    A node ID.
 *  @param fields (optional array)
 *    A list of fields to return.
 *  @return
 *    An array of node fields.
 */
  $nid = 16; // the node ID to be retrieved
  $key = array(
    new xmlrpcval($hash, "string"),
    new xmlrpcval($domain_name, "string"),
    new xmlrpcval($domain_time_stamp, "string"),
    new xmlrpcval($nonce, "string"),
    new xmlrpcval($nid, "int"),
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