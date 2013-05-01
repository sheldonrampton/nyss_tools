<?php
require_once './includes/common.inc';
$code = md5(uniqid(rand(), true));
$nonce = substr($code, 0, 20);
$domain = 'nyss.localhost';
$timestamp = (string) time();
$hash = hash_hmac('sha256', $timestamp .';'.$domain .';'. $nonce .';'.'node.get', '6d7d2a2529881abd436bc744b2606f9d');
$result = xmlrpc('http://nyss.localhost:8082/services/xmlrpc', 'node.get', $hash, $domain, $timestamp, $nonce, 107, array('body', 'title'));
if ($error = xmlrpc_error()) {
  print $error->message;
}
print '<pre>RESULT: ' . print_r($result, TRUE) . '</pre>';
?>