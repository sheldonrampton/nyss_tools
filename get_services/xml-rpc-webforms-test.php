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

include('xmlrpc-api-webforms.inc');

//$values = webform_responses(NULL, NULL, NULL, NULL, NULL, NULL, 0, 3); // this will return 3 responses from published OR unpublished webforms
//$values = webform_responses(NULL, NULL, NULL, NULL, NULL, NULL, 1, 3); // this will return 3 responses from published webforms only
$values = webform_responses(NULL, NULL, NULL, NULL, NULL, NULL, 2, 3); // this will return 3 responses from unpublished webforms only
print var_dump($values);

/**
 *  Get a list of NYSS email signups using an API provided by Drupal's Services module
 *
 * @param $nid
 *   Number. The webform's node ID.
 * @param $limit
 *   Number. The maximum number of responses to return (default 100). 
 *   If a limit of "0" is specified, all responses will be returned. Warning: setting no limit could result in timeout or memory errors!
 * @param $start_date
 *   Number. An optional start date of the date range for which results are desired.
 * @param $end_date
 *   Number. An optional end date of the date range for which results are desired.
 * @param $start_sid
 *   Number. An optional start submission ID number for the range from which results are desired.
 * @param $end_sid
 *   Number. An optional end submission ID number for the range from which results are desired.
 * @param $status
 *   Number. The published status of the webform.
 *       0 => return all results
 *       1 => only return submissions from published webforms
 *       2 => only return submissions from unpublished webforms
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
 *   where each instance of results1, results2, etc. is an array of the form:
 *     Array
 *         (
 *             'status' => the published status of the webform (0=unpublished, 1=published)
 *             'fields' => an array giving field definitions for the webform
 *             'sids' => Array(
 *                  [sid1] => [submission1]
 *                  [sid2] => [submission2]
 *                  ...
 *               )
 *         )
 *   and where each instance of submission1, submission2, etc. is an array of values from a single webform submission,
 *   as returned from function webform_service_submission_values().
 *
 * @see webform_service_submission_values() in the webform_service module
 */

function webform_responses($nid=NULL, $start_date=NULL, $end_date=NULL, $start_sid=NULL, $end_sid=NULL, $uid=NULL, $status=1, $limit=100) {
  $api_key        = 'PUT VALID API KEY HERE'; // put your API key here
  $domain_name    = 'PUT VALID DOMAIN HERE'; // put your domain name here
  
  $service = new WebformResponses($domain_name, $api_key);
  $result = $service->get(array(
    'nid' => $nid,
    'start_date' => $start_date,
    'end_date' => $end_date,
    'start_sid' => $start_sid,
    'end_sid' => $end_sid,
    'uid' => $uid,
    'status' => $status,
    'limit' => $limit,
  ));
  return $result;
}

?>