<?php
/**
 * @file
 * Processes a list of webform submissions by looking up their
 * Senate district.
 */

global $debug;
global $zips;
$debug = FALSE;
$zips = FALSE;
$filepath = $argv[1];
if (isset($argv[2]) && $argv[2] == '--debug') {
  $debug = TRUE;
}
if (isset($argv[2]) && $argv[2] == '--zips') {
  $zips = TRUE;
}
process_rows($filepath);

/**
 * Process a row of tab-text webform submission and add district lookups.
 *
 * @param String $filepath
 *   The path to a tab-text file exported from the Webform module.
 *
 * @return String
 *   A series of tab-text rows with Senate district information added.
 */
function process_rows($filepath) {
  global $debug;
  global $zips;
  $endloop = FALSE;
  if ($debug) {
    $i = 0;
  }
  $handle = @fopen($filepath, "r");
  if ($handle) {
    while (!$endloop && (($buffer = fgets($handle, 65536)) !== false)) {
      // If debug is true, stop looping after the first 10 items
      if ($debug) {
        $i++;
        if ($i > 99) {
          $endloop = TRUE;
        }
      }
      $buffer = trim($buffer);
      list(
        $serial,
        $sid,
        $time,
        $draft,
        $ip,
        $uid,
        $username,
        $first_name,
        $last_name,
        $grade_level,
        $school_name,
        $home_address,
        $city,
        $zip_code,
        $parent_name,
        $parent_email_address,
        $description,
        $name,
        $filesize
      ) = explode("\t", $buffer);

      $home_address = trim($home_address);
      $sid = str_replace('"', '', $sid);
      $state = "NY";
      if (substr($zip_code, 0, 1) === '0') {
        $state = "NJ";
      }
      // Output some diagnostic information if zip codes don't look like zip codes.
      $address = "$home_address, $city $state";
      if ($zips) {
        if (!is_numeric($zip_code) || strlen($zip_code) != 5) {
          print "$buffer\n";
          print "ADDRESS: $home_address\n";
          print "ZIP: $zip_code\n\n";
        }
      }
      else {
        $district = district_lookup($address, $zip_code);
        // If the lookup fails, wait a second and try again.
        if (!$district) {
          sleep(1);
          $district = district_lookup($address, $zip_code);
        }
        // If it still fails, give it 10 seconds.
        if (!$district) {
          sleep(10);
          $district = district_lookup($address, $zip_code);
        }
        // If that's not enough, just give up for christ's sake.
        if (!$district) {
          trigger_error("Failed district lookup", E_USER_NOTICE);
        }
        if ($debug) {
          print "ADDRESS: $address $zip_code\n";
          print "DISTRICT: $district\n";
        }
        // Note that in addition to adding the Senate district, this also
        // adds the state. This therefore makes it problematic to rerun this
        // script on post-processed records.
        else {
          $buffer = implode("\t", array(
            $serial,
            $sid,
            $time,
            $draft,
            $ip,
            $uid,
            $username,
            $first_name,
            $last_name,
            $grade_level,
            $school_name,
            $home_address,
            $city,
            $state,
            $zip_code,
            $parent_name,
            $parent_email_address,
            $description,
            $name,
            $filesize,
            $district,
          ));
          echo $buffer . "\n";
        }
      }
    }
    if (!feof($handle) && !$debug) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
}

/**
 * Lookup the Senate district for a single address.
 *
 * @param String $address
 *   The street address.
 * @param String $zip
 *   The zip code.
 *
 * @return String
 *   A district number.
 */
function district_lookup($address, $zip) {
  $url = 'http://pubgeo.nysenate.gov/api/v2/district/assign';
  $data = array(
    'addr1' => $address,
    'zip5' => $zip,
    'showMaps' => 'true',
  );
  $urlstring = '';
  foreach ($data as $key => $value) {
    $urlstring .= urlencode($key) . '=' . urlencode($value) . '&';
  }
  $ch = curl_init();
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 180);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $urlstring);
  $json = curl_exec($ch);
  curl_close($ch);
  $result = json_decode($json);
  if (isset($result->status) && $result->status == 'SUCCESS' && isset($result->districts->senate->district)) {
    return $result->districts->senate->district;
  }
  return FALSE;
}
