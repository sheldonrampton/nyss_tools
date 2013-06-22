<?php
$handle = @fopen("pdfs.txt", "r");
if ($handle) {
  $newnames = array();
  while (($buffer = fgets($handle, 4096)) !== false) {
    $buffer = trim($buffer);
    list($sid, $newname) = explode("\t", $buffer);
    if ($newname) {
      $newnames[$sid] = $newname;
    }
  }
  if (!feof($handle)) {
    echo "Error: unexpected fgets() fail\n";
  }
  fclose($handle);
}

$handle = @fopen("merged_list.tsv", "r");
if ($handle) {
  fgets($handle, 4096);
  fgets($handle, 4096);
  fgets($handle, 4096);
  $filenames = array();
  while (($buffer = fgets($handle, 4096)) !== false) {
    $buffer = str_replace(chr(00),'', $buffer);
    $buffer = trim($buffer);
    list($serial, $sid, $time, $draft, $ip, $uid, $username, $first_name, $last_name, $grade_level, $school_name, $home_address, $city, $zip_code, $parent_email_address, $description, $name, $filesize) = explode("\t", $buffer);
    $sid = str_replace('"', '', $sid);
    $buffer .= "\t\"" . $newnames[$sid] . "\"\n";
    echo $buffer;
  }
  if (!feof($handle)) {
    echo "Error: unexpected fgets() fail\n";
  }
  fclose($handle);
/*
  foreach ($filenames as $key => $value) {
    echo "$key: $value\n";
  }
*/
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
