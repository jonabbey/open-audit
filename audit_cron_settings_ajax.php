<?php
include "include_config.php";
include "include_functions.php";
include "include_audit_functions.php";

$smtp_auth      = ( $_POST['check_smtp_auth']      == 'true' ) ? '1' : '0' ;
$service_enable = ( $_POST['check_service_enable'] == 'true' ) ? '1' : '0' ;

mysql_connect($mysql_server,$mysql_user,$mysql_password) or die("Could not connect to DB<br>");
mysql_select_db($mysql_database);

$sql     = "SELECT * FROM audit_cron";
$result  = @mysql_query($sql);

if ( Windows_Server() ) {
  if (@mysql_num_rows($result) == 1) {
    $myrow = mysql_fetch_array($result);
    if ( $service_enable != $myrow['audit_cron_service_enable'] and $myrow['audit_cron_active'] == 1 ) {
      $error_list .= "<li>Stop the Web-Schedule service before changing service management<br>";
    }
    if ( $service_enable == 1 ) {
      if ( $myrow['audit_cron_service_name'] != $_POST['input_service_name'] and $myrow['audit_cron_active'] == 1 ) {
        $error_list .= "<li>Stop the Web-Schedule service before changing service names<br>";
      }
    }
  }
}

if ( $smtp_auth == '1' ) {
  if ( empty($_POST['input_smtp_user']) or empty($_POST['input_smtp_pass']) ) {
    $error_list .= "<li>The SMTP user/pass cannot be blank when SMTP authentication is enabled<br>";
  }
}

if ( Windows_Server() && $service_enable == '1' && empty($_POST['input_service']) ) {
  $error_list .= "<li>The service name cannot be left blank when enabled<br>";
}

if ( !preg_match("/^[1-9]([0-9]+)?$/",$_POST['input_interval']) ) {
  $error_list .= "<li>The polling interval must be a number with no leading zeros<br>";
}

if ( !preg_match("/^[1-9]([0-9]+)?$/",$_POST['input_smtp_port']) ) {
  $error_list .= "<li>The SMTP port must be a number with no leading zeros<br>";
}

/* Display any errors that occured, or submit the data */
if ( isset($error_list) ) {
  echo "<div class=\"formResult\"><img src=\"images/button_fail.png\"/>
        <b>Please correct the following form errors</b>
        <img src=\"images/button_fail.png\"/><br>
        <ul>"
        . $error_list . "</ul></div>";
}
else {
  $sql     = "SELECT * FROM audit_cron";
  $result  = @mysql_query($sql);
  if (@mysql_num_rows($result) == 0) { mysql_query("INSERT INTO `audit_cron` () VALUES ()"); }

  $aes_key = GetAesKey();
  $sql = "UPDATE audit_cron 
          SET audit_cron_service_name='{$_POST['input_service']}',
              audit_cron_service_enable='$service_enable',
              audit_cron_smtp_auth='$smtp_auth',
              audit_cron_smtp_user=AES_ENCRYPT('{$_POST['input_smtp_user']}','$aes_key'),
              audit_cron_smtp_pass=AES_ENCRYPT('{$_POST['input_smtp_pass']}','$aes_key'),
              audit_cron_smtp_server='{$_POST['input_smtp_server']}',
              audit_cron_smtp_port='{$_POST['input_smtp_port']}',
              audit_cron_smtp_from='{$_POST['input_smtp_from']}',
              audit_cron_web_address='{$_POST['input_web_address']}',
              audit_cron_interval='{$_POST['input_interval']}'";
  mysql_query($sql) or die("Could not update daemon settings: " . mysql_error() . "<br>");
  echo "<div class=\"formResult\"> <img src=\"images/button_success.png\"/>
        <strong>The Web-Schedule settings have been updated</strong></div>";
}
?>
