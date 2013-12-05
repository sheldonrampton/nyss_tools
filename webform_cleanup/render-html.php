#!/usr/bin/php
<?php
// Updated 12.13.2012 by Sheldon Rampton

// loop through each element in the $argv array
array_shift($argv);
$test = FALSE;
$schools_list = FALSE;
$param = array_shift($argv);
if ($param == '--test') {
  $test = TRUE;
}
else if ($param == '--schools') {
  $schools_list = TRUE;
}
else {
  array_unshift($argv, $param);
}
foreach($argv as $filename) {
  echo render_html($filename, $test, $schools_list);
}

function render_html($filename, $test, $schools_list) {
  $handle2 = NULL;
  $handle = @fopen($filename, "r");
  $districts = $schools = 0;
  if ($handle) {
    $filenames = array();
    $current_district = $current_school = '';
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
        $state,
        $zip_code,
        $parent_name,
        $parent_email_address,
        $description,
        $name,
        $filesize,
        $district
      ) = explode("\t", $buffer);
      if ($district != $current_district) {
        $first_school = TRUE;
        $current_school = '';
        $current_district = $district;
        if ($test || $schools_list) {
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
        if ($test) {
          $schools++;
          echo "NEW SCHOOL: $school_name\n";
        }
        else if ($schools_list) {
          $schools++;
          echo "$school_name\n";
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
      $row = "<li><a href=\"$name\">$first_name $last_name</a></li>\n";
      if (!$test && !$schools_list) {
        fwrite($handle2, $row);
      }
      else if (!$schools_list) {
        echo $row;
      }
    }
    if ($test || $schools_list) echo "DISTRICTS: $districts\SCHOOLS: $schools\n";
    if (!feof($handle)) {
      echo "Error: unexpected fgets() fail\n";
    }
    if (!$test && !$schools_list) {
      fwrite($handle2, "</ul>");
      fclose($handle2);
    }
    fclose($handle);
  }
}
