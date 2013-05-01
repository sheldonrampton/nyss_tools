<?php
// $Id$

/**
 *  @file
 *  xml-rpc-signups-test.php
 *
 *  This file provides an example of retrieving information from NYSenate.gov using the nyss_signup.get methods.
 *
 *  Project: NYSenate.gov
 *  Author: Sheldon Rampton
 *  Organization: New York State Senate
 *  Date: 2010-10-25
 *  Revised: 2010-10-25
 *
 *  For documentation, see the README file that accompanies this code as well as the developer documentation for
 *  NYSenate.gov at http://www.nysenate.gov/developers/apis
 */

include('xmlrpc-api-views.inc');

$values = senators_data(); // this will return all webform responses with submissions ids between 32 and 34
print var_dump($values);

/**
 *  Get a list of NYSS email signups using an API provided by Drupal's Services module
 *
 * @return
 *   An array of the form:
 *     Array
 *         (
 *             'start_sid' => the lowest submission id in the returned set
 *             'end_sid' => the highest submission id in the return set
 *             'start_date' => the lowest submission date in the returned set
 *             'end_date' => the highest submission date in the returned set
 *             'nids' => Array(
 *                  [nid1] => [results1]
 *                  [nid2] => [results2]
 *                  ...
 *               )
 *         )
 */

function senators_data($nid=NULL, $start_date=NULL, $end_date=NULL, $start_sid=NULL, $end_sid=NULL, $uid=NULL, $limit=100) {
  $api_key        = 'PUT VALID API KEY HERE'; // put your API key here
  $domain_name    = 'PUT VALID DOMAIN HERE'; // put your domain name here
  
  $service = new nyssViewsGet($domain_name, $api_key, 'http://ny2:8082/services/xmlrpc');
  $results = $service->get(array(
    'view_name' => 'senators_data',
  ));
  $values = array();
  foreach ($results as $result) {
    $values[] = array(
      'nid' => $result['nid'],
      'name' => $result['node_title'],
      'district' => $result['node_node_data_field_senators_district_node_data_field_district_number_field_district_number_value'],
      'email' => $result['node_data_field_email_field_email_email'],
      'url' => "http://www.nysenate.gov/senator/" . $result['node_data_field_email_field_path_value'],
    );
  }
  return $results;
}
