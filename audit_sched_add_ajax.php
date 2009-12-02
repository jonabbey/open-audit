<?php
include "include_config.php";
include "include_audit_functions.php";

$log_disable = ( $_POST['check_log_disable'] == "true" ) ? '1' : '0';
$email_log   = ( $_POST['check_email_log']   == "true" ) ? '1' : '0';

/* Check for general errors */
if ( $_POST['input_name'] == '' ) {
  $error_list .= "<li>You must name the schedule<br>";
}
else {
  mysql_connect($mysql_server,$mysql_user,$mysql_password) or die("Could not connect to DB<br>");
  mysql_select_db($mysql_database);
  $sql = "SELECT * FROM audit_schedules WHERE audit_schd_name LIKE '".$_POST['input_name']."'";
  $result = mysql_query($sql);
  if ( !@mysql_num_rows( $result ) < 1 AND $_POST['form_action'] != "edit" ) {
    $error_list .= "<li>That schedule name already exists. Please choose another one.<br>";
  }
}
  
if ( $_POST['select_config'] == 'nothing' ) {
  $error_list .= "<li>You must pick a configuration<br>";
}
if ( $_POST['select_sched_type'] == 'nothing' ) {
  $error_list .= "<li>You must pick a schedule type<br>";
}

/* Set hourly audits variable */
if ( $_POST['select_sched_type'] == 'hourly' ) {
  if ( $_POST['check_hours_between'] == "true" ) { $check_hours_between = 1; }
    else { $check_hours_between = 0; }
}

/* Check for misconfigured daily audits */
if ( $_POST['select_sched_type'] == 'daily' ) {
  if ( !preg_match("/^[0-9]{1,3}$/",$_POST['input_days_freq']) ) {
      $error_list .= "<li>The days frequency must be a number and less than 1000<br>";
  }
}

/* Check for misconfigured weekly audits */
$week_choices = @implode(",",$_POST['check_weekly']);
if ( $_POST['select_sched_type'] == 'weekly' ) {
  if ( count($_POST['check_weekly']) < 1 ) {
    $error_list .= "<li>You must select at least one day of the week<br>";
  }
}

/* Check for misconfigured monthly audits */
$month_choices = @implode(",",$_POST['check_monthly']);
if ( $_POST['select_sched_type'] == 'monthly' ) {
  if ( count($_POST['check_monthly']) < 1 ) {
    $error_list .= "<li>You must select at least one month<br>";
  }
}

/* If they entered a cron line, attempt to parse it first */
if ( $_POST['select_sched_type'] == 'crontab' ) {
  if ( !empty($_POST['input_cron_line']) ) {
    require_once("./lib/cronparser/CronParser.php");
    $cron = new CronParser();
    if ( ! $cron->calcLastRan($_POST['input_cron_line']) ) {
      $error_list .= "<li>Failed to parse the cron entry. Make sure it's valid...<br>";
    }
  }
  else{
    $error_list .= "<li>The cron entry cannot be blank<br>";
  }
}

$emails = array();

if ( $email_log == 1 ) {
  if ( empty($_POST['email_list']) ) {
    $error_list .= "<li>You need to specify at least one address to email to<br>";
  }
  else {
    /* Verify that the email addresses are somewhat correct */
    while ( $email = array_pop($_POST['email_list']) ) {
      if ( !preg_match("/^[\w!#$&%'*+=?`{|}~^.-]+@[A-Z0-9.-]+$/i", $email) ) {
        $error_list .= "<li>Email address in email to list is in bad format: {$email}<br>";
      }
      else {
        array_push($emails,$email);
      }
    }
  }
  if ( !preg_match("/^[\w!#$&%'*+=?`{|}~^.-]+@[A-Z0-9.-]+$/i", $_POST['input_email_replyto']) ) {
    $error_list .= "<li>The Reply-To email address is in bad format: {$_POST['input_email_replyto']}<br>";
  }
}

$email_list = @implode(";",$emails);

/* At least verify they're trying to use an image */
if ( ! empty($_POST['select_email_logo']) ) {
  $img = getimagesize("./images/headers/{$_POST['select_email_logo']}");
  if ( empty($img) ) {
    $error_list .= "<li>The logo doesn't seem to be an image file.<br>";
  }
}

/* Display any errors that occured, or submit the data */
if ( isset($error_list) ) {
  echo "<div id=\"form-result\"><img src=\"images/button_fail.png\"/>
          <b>Please correct the following form errors</b>
        <img src=\"images/button_fail.png\"/><br><ul>{$error_list}</ul></div>";
} else {
  /* Add the schedule to the table now */
  if ( $_POST['form_action'] == "edit" ) {
    $sql =
      "UPDATE audit_schedules SET 
        audit_schd_name='".$_POST['input_name']."',
        audit_schd_type='".$_POST['select_sched_type']."',
        audit_schd_strt_hr='".$_POST['select_gen_hour']."',
        audit_schd_strt_min='".$_POST['select_gen_min']."',
        audit_schd_hr_frq_hr='".$_POST['select_hourly_freq']."',
        audit_schd_hr_frq_min='".$_POST['select_hourly_start']."',
        audit_schd_hr_between='".$check_hours_between."',
        audit_schd_hr_strt_hr='".$_POST['select_hstrt_hour']."',
        audit_schd_hr_strt_min='".$_POST['select_hstrt_min']."',
        audit_schd_hr_end_hr='".$_POST['select_hend_hour']."',
        audit_schd_hr_end_min='".$_POST['select_hend_min']."',
        audit_schd_dly_frq='".$_POST['input_days_freq']."',
        audit_schd_wk_days='".$week_choices."',
        audit_schd_mth_day='".$_POST['select_monthly_day']."',
        audit_schd_mth_months='".$month_choices."',
        audit_schd_cfg_id='".$_POST['select_config']."',
        audit_schd_email_log='{$email_log}',
        audit_schd_email_list='{$email_list}',
        audit_schd_email_subject='{$_POST['input_email_subject']}',
        audit_schd_email_replyto='{$_POST['input_email_replyto']}',
        audit_schd_email_logo='{$_POST['select_email_logo']}',
        audit_schd_email_tt_text='{$_POST['select_tt_text']}',
        audit_schd_email_tt_html='{$_POST['select_tt_html']}',
        audit_schd_cron_line='".$_POST['input_cron_line']."',
        audit_schd_updated='1',
        audit_schd_log_disable='".$log_disable."'
      WHERE audit_schd_id='".$_POST['sched_id']."'";
  }
  else {
    $sql = 
        "INSERT INTO audit_schedules (
           audit_schd_name, audit_schd_cfg_id, audit_schd_type,
           audit_schd_strt_hr, audit_schd_strt_min, audit_schd_hr_frq_hr,
           audit_schd_hr_frq_min, audit_schd_hr_between, audit_schd_hr_strt_hr,
           audit_schd_hr_strt_min, audit_schd_hr_end_hr, audit_schd_hr_end_min,
           audit_schd_dly_frq, audit_schd_wk_days, audit_schd_mth_day,
           audit_schd_mth_months, audit_schd_log_disable, audit_schd_email_log,
           audit_schd_email_list, audit_schd_email_subject, audit_schd_email_replyto,
           audit_schd_email_logo, audit_schd_email_tt_text, audit_schd_email_tt_html,
           audit_schd_cron_line
         ) 
         VALUES (
           '".$_POST['input_name']."','".$_POST['select_config']."','".$_POST['select_sched_type']."',
           '".$_POST['select_gen_hour']."', '".$_POST['select_gen_min']."','".$_POST['select_hourly_freq']."',
           '".$_POST['select_hourly_start']."','".$check_hours_between."', '".$_POST['select_hstrt_hour']."',
           '".$_POST['select_hstrt_min']."','".$_POST['select_hend_hour']."','".$_POST['select_hend_min']."',
           '".$_POST['input_days_freq']."','".$week_choices."','".$_POST['select_monthly_day']."',
           '".$month_choices."', '".$log_disable."','{$email_log}',
           '{$email_list}','{$_POST['input_email_subject']}','{$_POST['input_email_replyto']}',
           '{$_POST['select_email_logo']}','{$_POST['select_tt_text']}','{$_POST['select_tt_html']}',
           '{$_POST['input_cron_line']}'
         )";
  }
  mysql_select_db($mysql_database);
  mysql_query($sql) or die("Could not add/update schedule: " . mysql_error() . "<br>");
  $form_action = ( $_POST['form_action'] == "edit" ) ? 'updated' : 'added';
  echo "<div id=\"form-result\">
        <img src=\"images/button_success.png\"/>  <b>The schedule has been {$form_action}</div>";
}
?>
