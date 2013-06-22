<?php

/**
 * @file
 * Renders HTML for each Senate district
 * Senate district.
 */

global $debug;
$debug = FALSE;
$filepath = $argv[1];
if (isset($argv[2]) && $argv[2] == '--debug') {
  $debug = TRUE;
}
process_rows($filepath, $debug);

function process_rows($filepath, $debug=FALSE) {
  $handle = @fopen($filepath, "r");
  if ($handle) {
    $filenames = array();
    $current_district = $current_school = '';
    while (($buffer = fgets($handle, 4096)) !== false) {
      $buffer = trim($buffer);
      list(
/*
        $serial,
        $sid,
        $time,
        $draft,
        $ip,
        $uid,
        $username,
        $student_name,
        $grade,
        $teacher_name,
        $school_name,
        $school_address,
        $home_address,
        $city_state_zip,
        $school_phone,
        $email_address,
        $name,
        $filesize,
        $district
*/
        $serial,
        $sid,
        $time,
        $draft,
        $ip,
        $uid,
        $username,
        $student_name,
        $grade,
        $teacher_name,
        $school_name,
        $school_address,
        $citystate,
        $zip,
        $school_phone,
        $email_address,
        $name,
        $filesize,
        $district
      ) = explode("\t", $buffer);
      $city_state_zip = "citystate $zip";
      if ($district != $current_district) {
        $first_school = TRUE;
        $current_school = '';
        $current_district = $district;
        if ($debug) {
          echo "NEW DISTRICT: $district\n";
          $districts++;
        }
        else {
          if ($handle2) {
            fwrite($handle2, "</ul>");
            fclose($handle2);
          }
          $handle2 = @fopen("district_$district.html", "w");
        }
      }
      if ($current_school != $school_name) {
        $current_school = $school_name;
        if ($debug) {
          $schools++;
          echo "NEW SCHOOL: $school_name\n";
        }
        else {
          if ($first_school) {
            $first_school = FALSE;
          }
          else {
            fwrite($handle2, "</ul>\n");
          }
          fwrite($handle2, "<h3>$school_name</h3>\n<ul>");
        }
      }
      $row = "<li><a href=\"$name\">$student_name</a></li>\n";
      if (!$debug) {
        fwrite($handle2, $row);
      }
      else {
        echo $row;
      }
    }
    if ($debug) echo "DISTRICTS: $districts\SCHOOLS: $schools\n";
    if (!feof($handle)) {
      echo "Error: unexpected fgets() fail\n";
    }
    if (!$debug) {
      fwrite($handle2, "</ul>");
      fclose($handle2);
    }
    fclose($handle);
  }
}

?>