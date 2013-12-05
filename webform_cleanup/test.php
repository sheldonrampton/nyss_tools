#!/usr/bin/php
<?php
// loop through each element in the $argv array
array_shift($argv);
$param = array_shift($argv);
if ($param == '--test') {
  echo "TEST!\n";
}
else {
  array_unshift($argv, $param);
}
foreach($argv as $value) {
  echo "$value\n";
}
