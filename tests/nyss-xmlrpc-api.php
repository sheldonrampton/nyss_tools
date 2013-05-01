<?php

/**
 * Requires the XML-RPC for PHP library (current version, 3.0.0beta).
 * http://phpxmlrpc.sourceforge.net/
 * http://sourceforge.net/projects/phpxmlrpc/files/phpxmlrpc/3.0.0beta/xmlrpc-3.0.0.beta.zip/download
 */
  include('xmlrpc/lib/xmlrpc.inc');

  $api_key        = '';
  
  /*
  $service = new NYSSNodeService('nysenate.gov', '55f7ea8c500758c8a9230fb9121abc36');
  $values = $service->get(array(
    'nid' => 16,
    'fields' => array('nid', 'title'),
  ));
  */
  
//  /*
  $service = new NYSSViewsService('', '');
  $values = $service->get(array(
    'view_name' => 'senator_news',
    'display_id' => 'block_1',
    'args' => array('Eric Adams'),
  ));
//  */

/*  
  $service = new NYSSViewsService('nysenate.gov', '55f7ea8c500758c8a9230fb9121abc36');
  $values = $service->get(array(
    'view_name' => 'senator_news',
    'display_id' => '',
    'args' => array('Eric Adams'),
  ));
*/

  print var_dump($values);
  
/**
 * Superclass for all NYSS Service classes.
 */
abstract class NYSSService
{
  // Property declarations
  protected $domain_name = NULL;
  protected $api_key = NULL;
  protected $endPoint = 'http://www.nysenate.gov/services/xmlrpc';
  protected $methodName = NULL; 
  protected $domain_time_stamp = NULL;
  protected $nonce = NULL;
  protected $hash = NULL;
  protected $client = NULL;
  protected $key = NULL;
  
  // Method declarations

  public function __construct($domain_name, $api_key, $endPoint = 'http://www.nysenate.gov/services/xmlrpc')
  {
    $this->domain_name = $domain_name;
    $this->api_key = $api_key;
    $this->endPoint = $endPoint;
    $this->domain_time_stamp = (string)time();
    $code = md5(uniqid(rand(), true));
    $this->nonce = substr($code, 0, 20);
  }
  
  /**
   *  Parameters for all Services methods:
   *  @param hash (required string)
   *    A valid API key.
   *  @param domain_name (required string)
   *    A valid domain for the API key.
   *  @param domain_time_stamp (required string)
   *    Time stamp used to hash key.
   *  @param nonce (required string)
   *    One time use nonce also used hash key.
   */
  public function get($params=NULL)
  {
    $this->client = new xmlrpc_client($this->endPoint);
    $this->client->return_type = 'xml';            
    $this->key = array(
      new xmlrpcval($this->hash, "string"),
      new xmlrpcval($this->domain_name, "string"),
      new xmlrpcval($this->domain_time_stamp, "string"),
      new xmlrpcval($this->nonce, "string"),
    );
    return NULL;
  }
}

/**
 * For handling the node.get method.
 */
class NYSSNodeService extends NYSSService
{

  public function __construct($domain_name, $api_key, $endPoint = 'http://www.nysenate.gov/services/xmlrpc')
  {
    parent::__construct($domain_name, $api_key, $endPoint);
    $this->methodName = 'node.get';
    $this->hash = hash_hmac('sha256', $this->domain_time_stamp .';'.$this->domain_name .';'. $this->nonce .';'. $this->methodName, $this->api_key);
  }

  // method declarations/overrides
  /**
   *  Additional parameters for the node.get method:
   *  @param nid (required int)
   *    A node ID.
   *  @param fields (optional array)
   *    A list of fields to return.
   *  @return
   *    An array of node fields.
   */
  public function get($params=NULL)
  {
    if (!is_array($params) || !isset($params['nid'])) {
      return NULL;
    }
    parent::get($params);
    $this->key[] = new xmlrpcval($params['nid'], "int");
    if (isset($params['fields']) && is_array($params['fields'])) {
      $fields = array();
      foreach ($params['fields'] as $field) {
        $fields[] = new xmlrpcval($field, "string");
      }
      $this->key[] = new xmlrpcval($fields, "array");
    }
    $message = new xmlrpcmsg($this->methodName, $this->key);
    $result = $this->client->send($message);
    return xmlrpc_decode($result->value());
  }
}

/**
 * For handling the views.get method.
 */
class NYSSViewsService extends NYSSService
{

  public function __construct($domain_name, $api_key, $endPoint = 'http://www.nysenate.gov/services/xmlrpc')
  {
    parent::__construct($domain_name, $api_key, $endPoint);
    $this->methodName = 'views.get';
    $this->hash = hash_hmac('sha256', $this->domain_time_stamp .';'.$this->domain_name .';'. $this->nonce .';'. $this->methodName, $this->api_key);
  }

  // method declarations/overrides
  /**
   *  Additional parameters for the views.get method:
   *  @param view_name (required string)
   *    View name.
   *  @param display_id (optional string)
   *    A display provided by the selected view.
   *  @param args (optional array)
   *    An array of arguments to pass to the view.
   *  @param offset (optional int)
   *    An offset integer for paging. If this is set limit will be ignored.
   *  @param limit (optional int)
   *    A limit integer for paging. If offset is set, this will be ignored.
   *  @param format_output (optional boolean)
   *    TRUE if view should be formatted, or only the view result returned (FALSE by default).
   *  @return
   *    An array of views fields, or formatted HTML if format_output is TRUE.
   */
  public function get($params=NULL)
  {
    if (!is_array($params) || !isset($params['view_name'])) {
      return NULL;
    }
    parent::get($params);
    // View name
    $this->key[] = new xmlrpcval($params['view_name'], "string");
    
    // A display provided by the selected view.
//    if (isset($params['display_id'])) {
      $this->key[] = new xmlrpcval(isset($params['display_id']) ? $params['display_id'] : 'default', "string");
//    }
    
    // An array of arguments to pass to the view.
    $args = array();
    if (isset($params['args']) && is_array($params['args'])) {
      foreach ($params['args'] as $arg) {
        $args[] = new xmlrpcval($arg, "string");
      }
    }
    $this->key[] = new xmlrpcval($args, "array");
    
    // An offset integer for paging. If this is set limit will be ignored.
//    if (isset($params['offset'])) {
      $this->key[] = new xmlrpcval(isset($params['offset']) ? $params['offset'] : 0, "int");
//    }
    
    // A limit integer for paging. If offset is set, this will be ignored.
//    if (isset($params['limit'])) {
      $this->key[] = new xmlrpcval(isset($params['limit']) ? $params['limit'] : 0, "int");
//    }
    
    // TRUE if view should be formatted, or only the view result returned (FALSE by default).
//    if (isset($params['format_output'])) {
      $this->key[] = new xmlrpcval(isset($params['format_output']) ? $params['format_output'] : FALSE, "boolean");
//    }

    $message = new xmlrpcmsg($this->methodName, $this->key);
    $result = $this->client->send($message);
    return xmlrpc_decode($result->value());
  }
}

/*
    print $this->domain_name."\n";
    print $this->api_key."\n";
    print $this->endPoint."\n";
    print $this->methodName."\n"; 
    print $this->domain_time_stamp."\n";
    print $this->nonce."\n";
    print $this->hash."\n\n";
//    print $this->client."\n";
//    print $this->key."\n\n";


    parent::get($params);

    print $this->domain_name."\n";
    print $this->api_key."\n";
    print $this->endPoint."\n";
    print $this->methodName."\n"; 
    print $this->domain_time_stamp."\n";
    print $this->nonce."\n";
    print $this->hash."\n\n";
//    print $this->client."\n";
    print $this->key."\n\n";
*/
?>