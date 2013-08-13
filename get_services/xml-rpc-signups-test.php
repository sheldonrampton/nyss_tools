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

include('xmlrpc-api-signups.inc');
include('nyss_signup_classes.inc');

$values = nyss_signup_service_get(1375818216, NULL, NULL, NULL, 100);
print var_dump($values);

/**
 *  Get a list of NYSS email signups using an API provided by Drupal's Services module
 *
 *  @param $last_sid
 *    The last signup ID that was previously retrieved.
 *  @param $last_timestamp
 *    The last timestamp that was previously retrieved.
 *  @return
 *    A SignupData object containing all accounts that have had new contacts signup since the last signup ID and timestamp.
 */
function nyss_signup_service_get($start_date=NULL, $end_date=NULL, $start_sid=NULL, $end_sid=NULL, $limit=100) {
  $api_key        = 'PUT VALID API KEY HERE'; // put your API key here
  $domain_name    = 'PUT VALID DOMAIN HERE'; // put your domain name here
  
  $service = new SignupGet($domain_name, $api_key);
  $values = $service->get(array(
    'start_date' => $start_date,
    'end_date' => $end_date,
    'start_sid' => $start_sid,
    'end_sid' => $end_sid,
    'limit' => $limit,
  ));

  // The values come through as an associative array, so now we have to create the SignupData object.
  $signups = new SignupData;
  $signups->startingId = $values['startingId'];
  $signups->endingId = $values['endingId'];
  foreach ($values['accounts'] as $account) {
    $x = new Account($account['name']);
    $list = new MailingList($account['lists'][0]['name']);
    $x->lists[] = $list;
    foreach ($account['contacts'] as $contact) {
      $c = new Contact( 
        $contact['id'],
        $contact['firstName'], 
        $contact['lastName'], 
        $contact['address1'], 
        $contact['address2'],
        $contact['city'],
        $contact['state'], 
        $contact['zip'],
        $contact['phoneMobile'], 
        $contact['issues'], 
        $contact['email']
      );
      $c->lists[] = $list;
      $x->contacts[] = $c;
    }
    $signups->accounts[] = $x;
  }
  return $signups;
}

?>