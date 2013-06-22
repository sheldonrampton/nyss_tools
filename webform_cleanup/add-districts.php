<?php

/**
 * @file
 * Processes a list of webform submissions by looking up their
 * Senate district.
 */

global $debug;
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
        $ip_address,
        $uid,
        $username,
        $name,
        $address,
        $school,
        $student_phone_number,
        $email,
        $cumulative_gpa,
        $nominator,
        $nominator_phone_number,
        $nominator_email,
        $essay,
        $filename,
        $filesize_kb,
        $list_of_school_activities
      ) = explode("\t", $buffer);

      $address = trim($address);
      $address_words = explode(" ", $address);
      $zip = array_pop($address_words);
      $address = implode(" ", $address_words);
      if ($zips) {
        if (!is_numeric($zip) || strlen($zip) != 5) {
          print "$buffer\n";
          print "ADDRESS: $address\n";
          print "ZIP: $zip\n\n";
        }
      }
      else {
        $district = district_lookup($address, $zip);
        if ($debug) {
          print "ADDRESS: $address $zip\n";
          print "DISTRICT: $district\n";
        }
        $buffer = implode("\t", array(
          $serial,
          $sid,
          $time,
          $draft,
          $ip_address,
          $uid,
          $username,
          $name,
          "$address $zip",
          $school,
          $student_phone_number,
          $email,
          $cumulative_gpa,
          $nominator,
          $nominator_phone_number,
          $nominator_email,
          $essay,
          $filename,
          $filesize_kb,
          $list_of_school_activities,
          $district,
        ));
        echo $buffer . "\n";
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
  $url = "http://pubgeo.nysenate.gov/api/xml/districts/extended/?";
  $data = array(
    'addr2' => $address,
    'zip5' => $zip,
    'key'=> '85b60bec-683c-43ef-8e3a-9388348f7103',
    'service' => 'yahoo',
  );
  $urlstring = '';
  foreach ($data as $key => $value) {
    $urlstring .= urlencode($key).'='.urlencode($value).'&';
  }
  $ch=curl_init();
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 180);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $urlstring);
  $html = curl_exec($ch);
  curl_close($ch);
  if (strpos($html, 'nysenate') !== FALSE) {
    $xml = simplexml_load_string(mb_convert_encoding($html, "UTF-8"));
    if ($xml) {
      $cd_arr = explode(" ", $xml->congressional->district);
      $cd_upper_arr = explode(" ",$xml->senate->district);
      $cd_upper = $cd_upper_arr[count($cd_upper_arr) - 1];
      $cd = $cd_arr[count($cd_arr) - 1];
      return $cd_upper;
    }
  }
  return FALSE;
}

function hex_chars($data) {
    $mb_chars = '';
    $mb_hex = '';
    for ($i=0; $i<mb_strlen($data, 'UTF-8'); $i++) {
        $c = mb_substr($data, $i, 1, 'UTF-8');
        $mb_chars .= '{'. ($c). '}';
        
        $o = unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
        $mb_hex .= '{'. hex_format($o[1]). '}';
    }
    $chars = '';
    $hex = '';
    for ($i=0; $i<strlen($data); $i++) {
        $c = substr($data, $i, 1);
        $chars .= '{'. ($c). '}';
        $hex .= '{'. hex_format(ord($c)). '}';
    }
    return array(
        'data' => $data,
        'chars' => $chars,
        'hex' => $hex,
        'mb_chars' => $mb_chars,
        'mb_hex' => $mb_hex,
    );
}

function hex_format($o) {
    $h = strtoupper(dechex($o));
    $len = strlen($h);
    if ($len % 2 == 1)
        $h = "0$h";
    return $h;
}
?>