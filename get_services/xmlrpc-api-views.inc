<?php

/**
 * Requires the XML-RPC for PHP library (current version, 3.0.0beta).
 * http://phpxmlrpc.sourceforge.net/
 * http://sourceforge.net/projects/phpxmlrpc/files/phpxmlrpc/3.0.0beta/xmlrpc-3.0.0.beta.zip/download
 */
include('xmlrpc-api.inc');


/**
 * For handling the views.get method.
 */
class nyssViewsGet extends drupalService
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
    $this->key[] = new xmlrpcval(isset($params['display_id']) ? $params['display_id'] : 'default', "string");

    // An array of arguments to pass to the view.
    $args = array();
    if (isset($params['args']) && is_array($params['args'])) {
      foreach ($params['args'] as $arg) {
        $args[] = new xmlrpcval($arg, "string");
      }
    }
    $this->key[] = new xmlrpcval($args, "array");

    // An offset integer for paging. If this is set limit will be ignored.
    $this->key[] = new xmlrpcval(isset($params['offset']) ? $params['offset'] : 0, "int");

    // A limit integer for paging. If offset is set, this will be ignored.
    $this->key[] = new xmlrpcval(isset($params['limit']) ? $params['limit'] : 0, "int");

    // TRUE if view should be formatted, or only the view result returned (FALSE by default).
    $this->key[] = new xmlrpcval(isset($params['format_output']) ? $params['format_output'] : FALSE, "boolean");

    $message = new xmlrpcmsg($this->methodName, $this->key);
    $result = $this->client->send($message);
    return xmlrpc_decode($result->value());
  }
}
