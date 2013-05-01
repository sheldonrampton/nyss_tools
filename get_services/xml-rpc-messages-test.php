<?php
// $Id$

/**
 *  @file
 *  xml-rpc-messages-test.php
 *
 *  This file provides an example of retrieving information from NYSenate.gov using the nyss_contact.messages methods.
 *
 *  Project: NYSenate.gov
 *  Author: Sheldon Rampton
 *  Organization: New York State Senate
 *  Date: 2011-09-04
 *  Revised: 2011-09-04
 *
 *  For further documentation, see the README file that accompanies this code as well as the developer documentation for
 *  NYSenate.gov at http://www.nysenate.gov/developers/apis
 */

include('xmlrpc-api-messages.inc');

$api_key        = 'PUT VALID API KEY HERE'; // put your API key here
$domain_name    = 'PUT VALID DOMAIN HERE'; // put your domain name here

$service = new ContactMessages($domain_name, $api_key);

$values = $service->get(array(
//  'start_date' => NULL,
//  'end_date' => NULL,
//  'start_mid' => NULL,
//  'end_mid' => NULL,
//  'source_form' => NULL,
//  'senator_short_name' => NULL,
//  'district_number' => NULL,
//  'limit' => NULL,
));

print var_dump($values);
