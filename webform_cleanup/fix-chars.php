<?php

/**
 * @file
 * Processes a list of webform submissions by looking up their
 * Senate district.
 */

global $debug;
$debug = FALSE;
$filepath = $argv[1];
if (isset($argv[2]) && $argv[2] == '--debug') {
  $debug = TRUE;
}
process($filepath);

/**
 * Process a row of tab-text webform submission and add district lookups.
 *
 * @param String $filepath
 *   The path to a tab-text file exported from the Webform module.
 *
 * @return String
 *   A series of tab-text rows with Senate district information added.
 */
function process($filepath) {
  global $debug;
  $endloop = FALSE;
  if ($debug) {
    $i = 0;
  }
  $handle = @fopen($filepath, "r");
  if ($handle) {
    $contents = fread($handle, 2000000);
    $contents = str_replace(chr(00),'', $contents);
//    echo print_r(hex_chars($contents), TRUE);
    $contents = str_replace("\r\n", "|", $contents);
    $contents = str_replace('""', "QUOTEQUOTE", $contents);
    $contents = substr($contents, 2);
    echo $contents;
    fclose($handle);
  }
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