<?php
include "include_config.php";
include "include_functions.php";
include "include_audit_functions.php";

/* Delete a configuration if it isn't tied to a schedule */
function Delete_Config($config_id,$db,$mysql_database) {
  /* Check if there is a schedule associated with it first */
  $sql  = "SELECT * FROM audit_schedules WHERE `audit_schd_cfg_id` = '".$config_id."'";
  mysql_select_db($mysql_database);
  $results =  @mysql_query($sql, $db) or die("Unable to query DB : ".mysql_error()."<br>");
  if (@mysql_num_rows($results) == 0) {
    /* Get the config name first */
    $sql  = "SELECT `audit_cfg_name` FROM audit_configurations WHERE `audit_cfg_id` = '".$config_id."'";
    $results =  @mysql_query($sql, $db) or die("Unable to query DB : ".mysql_error()."<br>");
    $myrow = mysql_fetch_array($results);
    $config_name = $myrow['audit_cfg_name'];

    /* Delete the configuration */
    $sql  = "DELETE FROM audit_configurations WHERE `audit_cfg_id` = '".$config_id."'";
    @mysql_query($sql, $db) or die("Unable to delete config : ".mysql_error()."<br>");

    /* Delete logs associated with it */
    $sql  = "DELETE FROM audit_log WHERE `audit_log_config_id` = '".$config_id."'";
    @mysql_query($sql, $db);

    /* Delete MySQL queries associated with it */
    $sql  = "DELETE FROM audit_mysql_query WHERE `audit_mysql_cfg_id` = '".$config_id."'";
    @mysql_query($sql, $db);

    echo "Deleted the Configuration: " . $config_name . "<br>";
  }
  else {
    echo "To delete the configuration it must not be associated with a schedule<br>";
  }
}

/* Audit a specific config with the Perl script */
function Run_Config($config_id,$db,$mysql_database) {
  $audit_bin = Get_Audit_Bin();
  mysql_select_db($mysql_database);
  $sql  = "SELECT `audit_cfg_name` FROM audit_configurations WHERE `audit_cfg_id` = '$config_id'";
  $results =  mysql_query($sql, $db) or die("Unable to query DB : ".mysql_error()."<br>");
  $myrow       = mysql_fetch_array($results);
  $config_name = $myrow['audit_cfg_name'];

  if ( empty($audit_bin) ) {
    $retval = 1;
  }
  elseif ( Windows_Server() ) {
    $retval = pclose(popen("start /b $audit_bin --run-config $config_id", 'r')); 
  }
  else {
    system("$audit_bin --daemon --run-config $config_id", $retval);
  }

  $result =
    ( $retval != '0' ) ?
    "Failed to run \"{$config_name}\"" :
    "Running Configuration \"{$config_name}\"";
  print $result;
}

function Delete_Schedule($sched_id,$db,$mysql_database) {
    /* Get the schedule name first */
    mysql_select_db($mysql_database);
    $sql  = "SELECT `audit_schd_name` FROM audit_schedules WHERE `audit_schd_id` = '$sched_id'";
    $results =  mysql_query($sql, $db) or die("Unable to query DB : ".mysql_error()."<br>");
    $myrow = mysql_fetch_array($results);
    $sched_name = $myrow['audit_schd_name'];

    /* Delete the configuration */
    $sql  = "DELETE FROM audit_schedules WHERE `audit_schd_id` = '$sched_id'";
    @mysql_query($sql, $db) or die("Unable to delete config : ".mysql_error()."<br>");

    /* Delete logs associated with it */
    $sql  = "DELETE FROM audit_log WHERE `audit_log_schedule_id` = '$sched_id'";
    @mysql_query($sql, $db);

    echo "Deleted the Schedule: $sched_name <br>";
}

/* Start/Stop a scheduled audited with the Perl script */
function Toggle_Schedule($sched_id,$db,$mysql_database) {
  mysql_select_db($mysql_database);
  $sql  = "SELECT audit_schd_active, audit_schd_name FROM audit_schedules WHERE `audit_schd_id` = '$sched_id'";
  $results =  mysql_query($sql, $db) or die("Unable to query DB : ".mysql_error()."<br>");
  $myrow = mysql_fetch_array($results);

  $sched_set = ( $myrow['audit_schd_active'] == 1 ) ? '0' : '1'; 
  $action    = ( $myrow['audit_schd_active'] == 1 ) ? 'Deactivated' : 'Activated'; 

  $sql  = "UPDATE audit_schedules SET audit_schd_active='{$sched_set}'
           WHERE `audit_schd_id` = '".$sched_id."'";
  mysql_query($sql, $db) or die("Unable to change schedule status : ".mysql_error()."<br>");
  echo "$action Schedule: {$myrow['audit_schd_name']}";
}

/* Start/Stop/Kill the cron daemon */
function Toggle_Cron($action,$db,$mysql_database) {
  $aes_key   = GetAesKey();
  $audit_bin = Get_Audit_Bin();

  mysql_select_db($mysql_database);
  $sql     = "SELECT * FROM audit_cron LIMIT 1";
  $results = mysql_query($sql, $db) or die("Unable to query audit_cron table : ".mysql_error()."<br>");
  $myrow   = mysql_fetch_array($results);

  $os = ( Windows_Server() ) ? 'windows' : 'linux';
  $tonull = ( $os == 'windows' ) ? '> NUL 2>&1' : '> /dev/null 2>&1';

  $cmd = array (
    "linux_start"   => "$audit_bin --cron-start --daemon $tonull",
    "stop"          => "$audit_bin --cron-stop $tonull",
    "windows_start" => "net start \"{$myrow['audit_cron_service_name']}\" $tonull",
    "windows_stop"  => "net stop \"{$myrow['audit_cron_service_name']}\" $tonull",
  );

  if ( Cron_Status($db,$mysql_database,'1') == 'running' ) {
    if ( $os == 'windows' ) {
      if ( $myrow['audit_cron_service_enable'] == 1 ) {
        system($cmd['windows_stop'], $retval);
      }
      else {
        $sql = "UPDATE audit_cron SET audit_cron_active='0'";
        mysql_query($sql, $db) or die("kill failure");
        echo "stop pending";
        exit;
      }
    }
    else {
      system($cmd['stop'], $retval);
    }
    $print_result = ( $retval != '0' ) ? 'stop failure' : 'stop success';
  }
  else {
    if ( $os == 'windows' and $myrow['audit_cron_service_enable'] != 1 ) {
      $retval = pclose(popen("start /b $audit_bin --cron-start --daemon $tonull", 'r'));
    }
    else {
      system($cmd[$os.'_start'], $retval);
    }
    $print_result = ( $retval != '0' ) ? 'start failure' : 'start success';
  }

  echo $print_result;
}

function Cron_Status($db,$mysql_database,$check) {
  $audit_bin = Get_Audit_Bin();

  mysql_select_db($mysql_database);
  $sql  = "SELECT * FROM audit_cron LIMIT 1";
  $results = mysql_query($sql, $db) or die("Unable to query daemon settings : ".mysql_error()."<br>");
  $myrow = mysql_fetch_array($results);

  if ( $myrow['audit_cron_pid'] == 0 ) {
    $print_result = 'stopped';
  }
  else {
    system("{$audit_bin} --check-pid", $retval);
    $print_result = ( $retval == 0 ) ? 'running' : 'stopped';
  }

  if ( $check == 1 ) { return $print_result; }

  print $print_result;
}

/* Get the last X ammount of cron log info and return it */
function Cron_Log($db,$mysql_database,$row_num) {
    mysql_select_db($mysql_database);
    $sql  = "SELECT * FROM cron_log ORDER BY cron_log_timestamp DESC LIMIT 10";
    $results =  @mysql_query($sql, $db);
    $cron_lines = array();

    if (@mysql_num_rows($results) != 0) {
      while ( $myrow = mysql_fetch_array($results) ) {
        $message   = $myrow['cron_log_message'];
        $timestamp = date('d/m/y h:i:s a',$myrow['cron_log_timestamp']);
        if ( strlen($message) > 40 ) {
          $message = substr($message,0,40) . "...";
        }
        $line = $timestamp . " - " . $message . "<br>\n";
        array_push($cron_lines, $line);
      }

      foreach ( array_reverse($cron_lines) as $line ) {
        echo $line;
      }
    }
    else {
      echo "<center><strong><i>Your log is currently empty</i></strong></center>";
    }
}

/* Pass this config info to the functions */
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password);

switch ($_POST['action']) {
  case "delete config":
    Delete_Config($_POST['config_id'],$db,$mysql_database);
    break;
  case "delete schedule":
    Delete_Schedule($_POST['schedule_id'],$db,$mysql_database);
    break;
  case "toggle schedule":
    Toggle_Schedule($_POST['schedule_id'],$db,$mysql_database);
    break;
  case "toggle cron":
    Toggle_Cron($_POST['type'],$db,$mysql_database);
    break;
  case "run config":
    Run_Config($_POST['config_id'],$db,$mysql_database);
    break;
  case "cron log":
    Cron_Log($db,$mysql_database,$_POST['row_num']);
    break;
  case "cron status":
    Cron_Status($db,$mysql_database,'0');
    break;
}

?>
