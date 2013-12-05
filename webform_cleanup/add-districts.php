#!/usr/bin/php
<?php
// loop through each element in the $argv array
array_shift($argv);
$test = FALSE;
$param = array_shift($argv);
if ($param == '--test') {
  $test = TRUE;
}
else {
  array_unshift($argv, $param);
}
foreach($argv as $filename) {
  echo add_districts($filename, $test);
}

function add_districts($filename, $test) {
  $handle = @fopen($filename, "r");
  if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
      $buffer = trim($buffer);
      
      /*
      $serial                 =>  What are you thankful for this year? Submission Details Serial
      $sid                    =>  SID
      $time                   =>  Time
      $draft                  =>  Draft
      $ip                     =>  IP Address
      $uid                    =>  UID
      $username               =>  Username
      $first_name             =>  First Name
      $last_name              =>  Last Name
      $grade_level            =>  Grade Level
      $school_name            =>  School Name
      $home_address           =>  Home Address
      $city                   =>  City/Town
      $zip_code               =>  Zip Code
      $parent_name            =>  Parent's First and Last Name
      $parent_email_address   =>  Parent's email address
      $description            =>  My submission could best be described as
      $name;                =>  Attach your submission here Name
      $filesize               =>  Filesize (KB)

      list($serial, $sid, $time, $draft, $ip, $uid, $username, $first_name, $last_name, $grade_level, $school_name, $home_address, $city, 
      $zip_code, $parent_email_address, $description, $name, $filesize) = explode("\t", $buffer);
      */


      list($serial, $sid, $time, $draft, $ip, $uid, $username, $first_name, $last_name, 
          $grade_level, $school_name, $home_address, $city, $zip_code, 
          $parent_name, $parent_email_address, $description, $name, $filesize) = explode("\t", $buffer);
      $sid = str_replace('"', '', $sid);
      $state = "NY";
      if (substr($zip_code, 0, 1) === '0') {
        $state = "NJ";
      }
      $address = "$home_address, $city $state $zip_code";
      $district = district_lookup($address);

      if ($test) {
        echo "$address\t$district\n";
/*
        if (substr($zip_code, 0, 1) === '0') {
          echo "New Jersey!\n";
          $state = "NJ";
        }
        else echo "$state\n";
*/

      }
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
    if (!feof($handle)) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
}

function district_lookup($address) {
  $url ="http://geo.nysenate.gov/api/xml/districts/addr/".urlencode($address)."?";
  $data = array('key'=> 'JsP46xRBHVQDVhL4XNrvM8VQDNDkA3');
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
