<?php

  include('nyss-xmlrpc-api.inc');
  $api_key        = 'PUT A VALID CODE HERE';
  $domain_name    = 'PUT A VALID DOMAIN HERE';

// Examples of the node.get method
  
  // Get the node ID (nid), title and location for node 107 (Senator Eric Adams)
  $service = new nyssNodeGet($domain_name, $api_key);
  $values = $service->get(array(
    'nid' => 107,
    'fields' => array('nid', 'title', 'field_location'),
  ));
  print var_dump($values);


// Examples of the views.get method
/*
  
  // Get the node ID (nid) and title for all committees
  $service = new nyssViewsGet($domain_name, $api_key);
  $values = $service->get(array(
    'view_name' => 'committees',
  ));
  print var_dump($values);
*/

/*
  // Get the node ID (nid) and title for all temporary committees
  $service = new nyssViewsGet($domain_name, $api_key);
  $values = $service->get(array(
    'view_name' => 'committees',
    'display_id' => 'page_2',
  ));
  print var_dump($values);
*/

/*
  // Get the node ID (nid) and title for the first two committees
  $service = new nyssViewsGet($domain_name, $api_key);
  $values = $service->get(array(
    'view_name' => 'committees',
    'limit' => 2,
  ));
  print var_dump($values);
*/

/*
  // Get a list of all committees, formatted as HTML
  $service = new nyssViewsGet($domain_name, $api_key);
  $values = $service->get(array(
    'view_name' => 'committees',
    'format_output' => TRUE,
  ));
  print var_dump($values);
*/

/*
  // Get the last 5 senator news items for Eric Adams
  $service = new nyssViewsGet($domain_name, $api_key);
  $values = $service->get(array(
    'view_name' => 'senator_news',
    'display_id' => 'block_1',
    'args' => array('Eric Adams'),
    'limit' => 5,
  ));
  print var_dump($values);
*/

/*
  // Get the last 5 senator news items for Eric Adams
  $service = new nyssViewsGet($domain_name, $api_key);
  $values = $service->get(array(
    'view_name' => 'district_map',
    'args' => array('106'),
  ));
  print var_dump($values);
*/

  
?>