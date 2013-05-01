<?php

$previous_update = svn_get_last_update("last_update");
$new_update_string = svn_update();
svn_write_new_update_string($new_update_string, "last_update");
$current_update = svn_get_last_update("last_update");
print "From $previous_update to $current_update.\n";
$current_dir = getcwd();
// Update James Tunick's branch
chdir("/Users/sheldonrampton/Sites/nysenate/branches/james");
print getcwd() . "\n";
svn_rampy_merge_commit($previous_update, $current_update);
// Update Dan Pozzie's branch
chdir("/Users/sheldonrampton/Sites/nysenate/branches/dan");
print getcwd() . "\n";
svn_rampy_merge_commit($previous_update, $current_update);
// Update staging
chdir("/Users/sheldonrampton/Sites/nysenate/staging/docroot");
print getcwd() . "\n";
svn_rampy_merge_commit($previous_update, $current_update);
// Update production
chdir("/Users/sheldonrampton/Sites/nysenate/deploy/docroot");
print getcwd() . "\n";
svn_rampy_merge_commit($previous_update, $current_update, FALSE);

chdir($current_dir);
print "Back to " . getcwd() . "\n";

//rampy_merge () { svn merge "$@" https://bal-1.prod.hosting.acquia.com/nysenate/branches/rampy ; }


function svn_rampy_merge_commit($from, $to, $include_commit = TRUE) {
  putenv("USERNAME=nysenate");
  $sys = system("svn update");
  $sys = system("svn merge -r$from:$to https://bal-1.prod.hosting.acquia.com/nysenate/branches/rampy");
  if ($include_commit) {
    $sys = system("svn commit -m 'merge changes -r$from:$to from rampy branch'");
  }
}

function svn_get_last_update($filename) { 
  $handle = fopen($filename, "r");
  $buffer = fgets($handle); // Read a line.
  $pattern = '/(\d+)/';
  preg_match($pattern, $buffer, $matches);
  return $matches[0];
  fclose($handle);
}

function svn_update() {
  putenv("USERNAME=PUT VALID USERNAME HERE");
  //print getenv("USERNAME");
  $sys = system("svn update");
  return $sys;
}

function svn_write_new_update_string($new_string, $filename) {
  $handle = fopen($filename, "w");
  fwrite($handle, $new_string);
}

function svn_pwd() {
  $pwd = system("pwd");
  print $pwd;
}