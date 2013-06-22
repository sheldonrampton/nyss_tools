<?php
$test = FALSE;
$handle = @fopen("with_districts.txt", "r");
if ($handle) {
  $filenames = array();
  while (($buffer = fgets($handle, 4096)) !== false) {
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
      $parent_email_address,
      $description,
      $name,
      $filesize,
      $converted_filename,
      $district
    ) = explode("\t", $buffer);
    if ($converted_filename == '""') {
      $converted_filename = $name;
      $unchanged++;
    }
    else if ($converted_filename != $name) {
      $pdfs++;
      $converted_filename = str_replace('"','', $converted_filename);
      $converted_filename = '"http://www.nysenate.gov/files/webform/pdfs/' . $converted_filename . '"';
    }
    else {
      $already_changed++;
    }
    if ($test) echo $converted_filename . "\n";

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
      $zip_code,
      $parent_email_address,
      $description,
      $name,
      $filesize,
      $converted_filename,
      $district,
    ));
    if (!$test) echo $buffer . "\n";
    $count ++;
  }
  if ($test) echo "ALREADY CHANGED: $already_changed\nUNCHANGED: $unchanged\nPDFS: $pdfs\nTOTAL: $count\n";
  if (!feof($handle)) {
    echo "Error: unexpected fgets() fail\n";
  }
  fclose($handle);
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
