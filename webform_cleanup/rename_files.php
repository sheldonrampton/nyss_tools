<?php
$handle = @fopen("what_are_you_thankful_for_this_year.tsv", "r");
if ($handle) {
  while (($buffer = fgets($handle, 4096)) !== false) {
    $buffer = str_replace(chr(00),'', $buffer);
    $buffer = trim($buffer);
    $buffer = str_replace('"', '', $buffer);
    list($serial, $sid, $time, $draft, $ip, $uid, $username, $student_name, $grade, $teacher_name, $school_name, $school_address, $home_address, $city_state_zip, $school_phone, $email_address, $name, $filesize) = explode("\t", $buffer);

    if ($name) {
      $filename = str_replace('http://www.nysenate.gov/files/webform/', '', $name);
      echo "$sid\t$sid-$filename\t$filename\n";
      $filename = urldecode($filename);
      rename($filename, "$sid-$filename");
    }
  }
  if (!feof($handle)) {
    echo "Error: unexpected fgets() fail\n";
  }
  fclose($handle);
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
