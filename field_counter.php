<?php
/**
 * @file
 * Field Counter
 *
 * This module iterates over a tab text file and counts the number
 * of unique instances of each column (field) value.
 */

global $debug;
$debug = $count_ips = $count_urls = $count_timestamps = FALSE;
array_shift($argv);
$filepath = array_shift($argv);
$params = array();
foreach ($argv as $arg) {
  switch ($arg) {
    case '--debug':
      $debug = TRUE;
      break;

    case '--ips':
      $count_ips = TRUE;
      break;
    
    case '--urls':
      $count_urls = TRUE;
      break;

    case '--timestamps':
      $count_timestamps = TRUE;
      break;

    default:
      $regex = '/^--(.+)=(.+)$/i';
      if (preg_match_all($regex, $arg, $match)) {
        $params[$match[1][0]] = $match[2][0];
      }
  }
}
process_rows($filepath);

/**
 * Process a row of tab-text webform submission and add district lookups.
 *
 * @param String $filepath
 *   The path to a tab-text file with the following three fields:
 *      timestamp
 *      ip
 *      url
 *
 * @return String
 *   A series of tab-text rows with Senate district information added.
 */
function process_rows($filepath) {
  global $debug, $count_ips, $count_urls, $count_timestamps, $params;
  $endloop = FALSE;
  if ($debug) {
    $i = 0;
  }
  $handle = @fopen($filepath, "r");
  if ($handle) {
    $timestamps = $ips = $urls = array();
    if (isset($params['min']) && isset($params['max'])) {
      $min = $params['min'];
      $max = $params['max'];
      while ($min <= $max) {
        $timestamps[$min] = 0;
        $min++;
      }
    }
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
        $timestamp,
        $ip,
        $url
      ) = explode("\t", $buffer);
      if ($count_timestamps) {
        $timestamps[$timestamp] = isset($timestamps[$timestamp]) ? ($timestamps[$timestamp] + 1) : 1;
      }
      if ($count_ips) {
        $ips[$ip] = isset($ips[$ip]) ? ($ips[$ip] + 1) : 1;
      }
      if ($count_urls) {
        $urls[$url] = isset($urls[$url]) ? ($urls[$url] + 1) : 1;
      }
    }
    if (!feof($handle) && !$debug) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
  if ($count_timestamps) {
    foreach ($timestamps as $timestamp => $count) {
      echo "$timestamp\t$count\n";
    }
  }
  if ($count_ips) {
    foreach ($ips as $ip => $count) {
      echo "$ip\t$count\n";
    }
  }
  if ($count_urls) {
    foreach ($urls as $url => $count) {
      echo "$url\t$count\n";
    }
  }
}
?>
