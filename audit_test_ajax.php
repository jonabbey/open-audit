<?php
include "include_config.php";
include "include_audit_functions.php";

function TestSettings($test_type,$config_id,$db,$mysql_database) {
  /* Check if config at least exists */
  $sql  = "SELECT * FROM audit_configurations WHERE `audit_cfg_id` = '".$config_id."'";
  mysql_select_db($mysql_database);
  $results =  @mysql_query($sql, $db) or die("Unable to query DB : ".mysql_error()."<br>");
  if (@mysql_num_rows($results) != 0 or !empty($_POST['email']) ) {
    /* check to make sure the the settings were saved */
    $myrow = mysql_fetch_array($results);
    if ( $test_type == "ldap" && $myrow['audit_cfg_type'] != "domain" ) {
      echo "The configuration is not set to use LDAP<br>";
      echo "Make sure you submit your changes before testing the connection!<br>";
    }
    else if ( $test_type == "mysql" && $myrow['audit_cfg_type'] != "mysql" ) {
      echo "The configuration is not set to use MySQL<br>";
      echo "Make sure you submit your changes before testing the connection!<br>";
    }
    else if ( $test_type == "nmap" && $myrow['audit_cfg_action'] != "nmap" ) {
      echo "The configuration is not set to use Nmap<br>";
      echo "Make sure you submit your changes before testing the connection!<br>";
    }
    else {
      $audit_bin = Get_Audit_Bin();

      if ( empty($audit_bin) ) {
        echo "Cannot find an audit.exe/audit.pl/audit file";
        exit;
      }

      if ( empty($_POST['email']) ) {
        $test_results = array();
        $test = ( $test_type == 'ldap' or $test_type == 'mysql' ) ? 'query' : $test_type ;
        exec("{$audit_bin} --test-$test $config_id 2>&1",$test_results);
        foreach ( $test_results as $line ) { echo $line . "<br>"; }
      }
      else {
        TestSMTP($_POST['email'],$audit_bin);
      }
    }
  }
  else {
    echo "No such audit configuration in the database<br>";
  }
}

function TestSMTP($email,$audit_bin) {

  if ( !preg_match("/^[\w!#$&%'*+=?`{|}~^.-]+@[A-Z0-9.-]+$/i", $email) ) {
    echo "Please enter a valid email address";
    exit;
  }

  system("{$audit_bin} --test-smtp " . escapeshellarg($_POST['email']),$result);

  if ( $result == 0 ) {
    echo "<b><i><font color=\"green\">Email was sent</font></i></b>";
  }
  else {
    echo "<font color=\"red\"><b><i>Problems sending email!</i></b></font>";
  }
}

function Get_Next_Run($cron_line) {
  require_once("./lib/cronparser/CronParser.php");
  $cron = new CronParser();

  if ( ! $cron->calcLastRan($cron_line) ) {
    echo "<b>Invalid Cron Entry</b>";
    exit;
  }

  $audit_bin = Get_Audit_Bin();
  $entry = escapeshellarg($cron_line);

  $out = `{$audit_bin} --test-cron $entry`;
  $next_run = date('D M jS Y h:i:s A',$out);

  echo $next_run;
}

if ( $_POST['type'] == 'cron' ) {
  Get_Next_Run($_POST['cron_line']);
}
else {
  if ( $_POST['type'] == 'smtp' && empty($_POST['email']) ) {
    echo "Please enter an email address to send to";
    exit;
  }
  $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
  TestSettings($_POST['type'],$_POST['config_id'],$db,$mysql_database);
}

?>
