<?php
// $Id$

/**
 * @file
 * Common functionality for querying the OpenLeg API.
 *
 * Documentation of the OpenLeg API is available at:
 * http://senatedev.senate.state.ny.us/wiki/legxml/Notes_on_OpenLeg_APIPHP_library
 */

define('OPENLEG_ROOT', 'http://open.nysenate.gov/legislation/');
define('OPENLEG_STAGING', 'http://open-staging.nysenate.gov/legislation/');

/**
 * Superclass for all OpenLeg classes.
 */
abstract class OpenLegObject
{
  // Property declarations
  public $data = NULL; // for storing and manipulating the object's data internally (a SimpleXML object?)
  public $source = OPENLEG_ROOT;
  public $xml = NULL;

  // Method declarations

  public function get() {
      $this->data = NULL;
  }
  // Return an HTML version of the object
  public function html() {
    return '';
  }
  // Return an XML version of the object
  public function xml() {
    return '';
  }
  // Return a CSV version of the object
  public function csv() {
    return '';
  }
  // Return a JSON version of the object
  public function json() {
    return '';
  }
  // Return an RSS version of the object
  public function rss() {
    return '';
  }
}

/**
 * For handling lists of OpenLeg objects.
 */
class OpenLegItemList extends OpenLegObject
{
  // property declarations
  public $count; // the total number of items returned by the search

  // method declarations/overrides
  public function get() {
      $this->data = NULL;
  }
  public function item($n) {
    // return the $nth item in the list
  }
}

/**
 * For handling Lucene searches retrieved via the Search API.
 */
class OpenLegSearch extends OpenLegItemList
{
  // property declarations
  public $count; // the total number of items returned by the search

  // method declarations/overrides
  public function get() {
      $this->data = NULL;
  }
}

/**
 * For handling listings retrieved via the Collections API.
 * DEPRECATED?
 */
class OpenLegCollection extends OpenLegItemList
{
  // property declarations

  // method declarations/overrides
  public function get() {
      $this->data = NULL;
  }
}

class OpenLegItem extends OpenLegObject
{
  // property declarations
  public $type = NULL;
  public $id = NULL;

  // method declarations/overrides
  public function __construct($type, $id=NULL, $source=OPENLEG_ROOT, $xml=NULL)
  {
    $this->type = $type;
    $this->id = $id;
    $this->source = $source;
    $this->xml = $xml;
    if ($xml) {
      $this->xml($xml);
    }
    else if ($this->id) {
      $this->get();
    }
  }
  public function xml($xml=NULL) {
    // If XML is provided, load it and construct the object's data as a SimpleXMLElement
    if ($xml) {
      $this->xml = $xml;
      $this->data = @simplexml_load_string($xml);
      $this->id = (string)$this->data->attributes()->id;
    }
    // If no XML is provided, RETURN the XML that was used to construct the object.
    else if ($this->xml) {
      return $this->xml;
    }
    else if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return $this->data->asXml();
    }
  }
  public function get() {
    $this->xml = openleg_curl_request($this->source . 'api/1.0/xml/' . $this->type . '/' . urlencode($this->id));
    $this->xml = str_replace("\0","",$this->xml); // hacky fix for XML bug
    $this->data = @simplexml_load_string($this->xml);
  }
}

/**
 * A single meeting.
 */
class OpenLegMeeting extends OpenLegItem
{
  // property declarations

  // method declarations/overrides
  public function __construct($id=NULL, $source=OPENLEG_ROOT, $xml=NULL){
    parent::__construct('meeting', $id, $source, $xml);
  }
  public function type() {
    return $this->type;
  }
  public function id() {
    return $this->id;
  }
  public function source() {
    return $this->source;
  }
  public function meetingDateTime() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return (string)$this->data->attributes()->meetingDateTime;
    }
  }
  public function meetday() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return (string)$this->data->attributes()->meetday;
    }
  }
  public function location() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return (string)$this->data->attributes()->location;
    }
  }
  public function committeeName() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return (string)$this->data->attributes()->committeeName;
    }
  }
  public function committeeChair() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return (string)$this->data->attributes()->committeeChair;
    }
  }
  public function attendees() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return $this->data->attendees[0];
    }
  }
  public function bills() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return $this->data->bills[0];
    }
  }
  public function notes() {
    if ($this->data && get_class($this->data) == 'SimpleXMLElement') {
      return (string)$this->data->notes;
    }
  }
}

/**
 * A single bill.
 */
class OpenLegBill extends OpenLegItem
{
  // property declarations
  public $type = 'bill';

  // method declarations/overrides
}

/**
 * A single calendar.
 */
class OpenLegCalendar extends OpenLegItem
{
  // property declarations
  public $type = 'calendar';

  // method declarations/overrides
}

/**
 * A single transcript.
 */
class OpenLegTranscript extends OpenLegItem
{
  // property declarations
  public $type = 'transcript';

  // method declarations/overrides
}

/**
 * A single vote.
 */
class OpenLegVote extends OpenLegItem
{
  // property declarations
  public $type = 'vote';

  // method declarations/overrides
}

/**
 * A single action.
 */
class OpenLegAction extends OpenLegItem
{
  // property declarations
  public $type = 'action';

  // method declarations/overrides
}

function openleg_curl_request($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function openleg_retrieve($path, $source=OPENLEG_ROOT) {
  $xml = openleg_curl_request($source . 'api/1.0/xml/' . str_replace("%2F", "/", urlencode($path)));
  $xml = str_replace("\0","",$xml); // hacky fix for bad XML bug
  return @simplexml_load_string($xml);
}

function openleg_retrieve_xml($path, $source=OPENLEG_ROOT) {
  $xml = openleg_curl_request($source . 'api/1.0/xml/' . str_replace("%2F", "/", urlencode($path)));
  $xml = str_replace("\0","",$xml); // hacky fix for bad XML bug
  return $xml;
}

/**
 * Query OpenLeg via Lucene search.
 * 
 * @param $params
 *   An associative array of parameters. The search needs at least one parameter for either 'type' or 'term'. All
 *   other parameters are optional. Each value of The full list of parameters is:
 *     type => meeting, bill
 *     term => A Lucene 3.0 format query.
 *     format => xml, json, html or rss
 *     pageidx => the page number of search results. If no value is entered, the default is 1.
 *     pagesize => the number of results per page. If no value is entered, the default is 20.
 *     sort => The field to sort by: 'oid', 'sponsor', 'chair', 'title' or 'when'
 *     sortorder => to sort in ascending or descending order. Options: 'true' for descending order (the default), 'false' for ascending order.
 *   Example:
 *     openleg_search(array('type' => 'meeting', term => 'diaz education', 'format => 'xml'))
 * @param $source
 *   The OpenLeg source site to search. Options are OPENLEG_ROOT for the public site, or OPENLEG_STAGING for the staging server,
 *   where testing is done on new OpenLeg features prior to deployment on the public site.
 * @return
 *   The search result.
 */
function openleg_lucene_search($params=array(), $source=OPENLEG_ROOT) {
  $params += array(
    'type' => NULL,
    'term' => NULL,
    'format' => 'xml',
    'pageidx' => 1,
    'pageSize' => 20,
    'sort' => NULL, // 'when',
    'sortorder' => NULL, // 'true',

    'startdate' => NULL,
    'enddate' => NULL,
    'status' => NULL,
    'memo' => NULL,
    'sameas' => NULL,
    'sponsor' => NULL,
    'cosponsors' => NULL,
    'committee' => NULL,
    'location' => NULL,

  );
  if (!$params['type'] && !$params['term']) {
    return FALSE;
  }
  if ($params['sort']) {
    $params['sortorder'] = ($params['sortorder'] === FALSE || strtolower($params['sortorder']) == 'false') ? 'false' : 'true';
  }
  else {
    $params['sortorder'] = NULL;
  }
  foreach ($params as $key => $val) {
    if ($val) {
      $terms[] = "$key=" . urlencode($val);
    }
  }
  $path .= 'search?' . implode('&', $terms);
//  drupal_set_message($source . $path); // for testing with Drupal module
  $data = openleg_curl_request($source . $path);
  return $data;
}

/**
 * Return a field's value for the SimpleXMLElement representation of a meeting.
 * 
 * @param $simplexml
 *   A SimpleXMLElement object.
 * @param $field
 *   The name of the field to be returned.
 * @return
 *   A string, unless the value of $field is 'attendees' or 'bills', in which case
 *   the function returns a SimplXMLElement object.
 */
function openleg_meeting($simplexml, $field = 'id') {
  switch ($field) {
    case 'date_time':
    case 'meetingDateTime':
      return (string)$simplexml->attributes()->meetingDateTime;
      break;
    case 'day_of_week':
    case 'meetday':
      return (string)$simplexml->attributes()->meetday;
      break;
    case 'location':
      return (string)$simplexml->attributes()->location;
      break;
    case 'committee_name':
    case 'committeeName':
      return (string)$simplexml->attributes()->committeeName;
      break;
    case 'committee_chair':
    case 'committeeChair':
      return (string)$simplexml->attributes()->committeeChair;
      break;
    case 'attendees':
      return $simplexml->attendees[0];
      break;
    case 'bills':
      return $simplexml->bills[0];
      break;
    case 'note':
    case 'notes':
      return (string)$simplexml->notes;
      break;
    case 'id':
    default:
      return (string)$simplexml->attributes()->id;
      break;
  }
}

/**
 * Return a single attribute from a SimpleXMLElement representation of an OpenLeg object.
 * 
 * @param $simplexml
 *   A SimpleXMLElement object.
 * @param $field
 *   The name of the attribute to be returned.
 * @return
 *   A string.
 */
function openleg_attribute($simplexml, $field = 'name') {
  return (string)$simplexml->attributes()->{$field};
}

/**
 * Return a field's value for the SimpleXMLElement representation of a bill.
 * 
 * @param $simplexml
 *   A SimpleXMLElement object.
 * @param $field
 *   The name of the field to be returned.
 * @return
 *   A string.
 */
function openleg_bill($simplexml, $field = 'sponsor') {
  switch ($field) {
    case 'year':
      return (string)$simplexml->attributes()->year;
      break;
    case 'id':
      return (string)$simplexml->attributes()->id;
      break;
    case 'current_committee':
    case 'currentCommittee':
      return (string)$simplexml->currentCommittee;
      break;
    case 'law_section':
    case 'lawSection':
      return (string)$simplexml->lawSection;
      break;
    case 'same_as':
    case 'sameAs':
      return (string)$simplexml->sameAs;
      break;
    case 'sponsor':
      return (string)$simplexml->sponsor->attributes()->fullname;
      break;
    case 'summary':
      return (string)$simplexml->summary;
      break;
    case 'title':
      return (string)$simplexml->title;
      break;
  }
}