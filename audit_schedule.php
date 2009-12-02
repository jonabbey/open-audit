<?php
$page = "admin";
include "include.php";
include "include_audit_functions.php";

  $sql  = "SELECT * FROM audit_schedules WHERE `audit_schd_id` = '".$_GET['sched_id']."'";
  $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
  $result  = mysql_query($sql, $db);
  $myrow   = @mysql_fetch_array($result);
  $emails  = $myrow['audit_schd_email_list'];
  $dly_frq = 
    ( empty($myrow['audit_schd_dly_frq']) ) ?
    '1' : $myrow['audit_schd_dly_frq'] ;

  $form_action = ( isset($_GET['sched_id']) ) ? 'edit' : 'add' ; 

  $head = 
    ( isset($_GET['sched_id']) ) ?
    "Editing Schedule \"{$myrow['audit_schd_name']}\"" :
    'Add a Schedule';
?>
<link media="screen" rel="stylesheet" type="text/css" href="audit_sched.css" />
<script type='text/javascript' src="javascript/audit_config.js"></script>
<script type='text/javascript' src="javascript/audit_sched.js"></script>
<td valign="top">
<div class="main_each">
  <div class="form-result"><span id="form_result_success"></span></div>
  <?php
    /* Check if the schedule exists. Do not show the form if none exists */
    if (@mysql_num_rows($result) != 0 or !isset($_GET['sched_id']) ) {
  ?>
  <div class="submit-push">&nbsp;</div>
  <div class="header"><?php echo $head ?></div>
  <br><br>
  <form action="javascript:get('sched','<?php echo $form_action ?>','<?php echo $_GET['sched_id'] ?>');" method="post" id="form_sched">
    <fieldset><legend>General Settings</legend>
        <label for="input_name">Name</label>
        <input type="text" size="20" id="input_name" value="<?php echo $myrow['audit_schd_name'] ?>"/>
        <br>
        <label for="select_config">Configuration</label>
        <?php $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
              Get_Audit_Configs($db,$myrow['audit_schd_cfg_id']); ?>
        <br>
        <label for="select_sched_type">Schedule Type</label>
        <select size="1" onChange="SwitchSchedType(this)" id="select_sched_type">
          <option value="nothing">Schedule Type</option>
          <option value="nothing">-------</option>
          <option value="hourly" <?php if ( $myrow['audit_schd_type'] == "hourly" ) { echo "SELECTED"; } ?> >Hourly</option>
          <option value="daily" <?php if ( $myrow['audit_schd_type'] == "daily" ) { echo "SELECTED"; } ?> >Daily</option>
          <option value="weekly" <?php if ( $myrow['audit_schd_type'] == "weekly" ) { echo "SELECTED"; } ?> >Weekly</option>
          <option value="monthly" <?php if ( $myrow['audit_schd_type'] == "monthly" ) { echo "SELECTED"; } ?> >Monthly</option>
          <option value="crontab"<?php if ( $myrow['audit_schd_type'] == "crontab" ) { echo "SELECTED"; } ?> >Cron Entry</option>
        </select>
        <br>
        <label for="select_gen_hour">Starting Time</label>
        <select size="1" id="select_gen_hour">
          <?php Get_Select_Options("0","23",$myrow['audit_schd_strt_hr']); ?>
        </select>:
        <select size="1" id="select_gen_min">
          <?php Get_Select_Options("0","59",$myrow['audit_schd_strt_min']); ?>
        </select>
        <br>
        <label for="check_log_disable">Disable Logging</label>
        <input type="checkbox" onclick="toggleLogging(this)" size="20" id="check_log_disable" <?php if ( $myrow['audit_schd_log_disable'] == 1 ) { echo "CHECKED"; } ?>/>
        <br><br>
        <label for="check_email_log">Email Audit Results</label>
        <input type="checkbox" onclick="toggleEmail(this)" size="20" id="check_email_log" <?php if ( $myrow['audit_schd_email_log'] == 1 ) { echo "CHECKED"; } ?>/>
    </fieldset>
    <fieldset id="fs_hourly"><legend>Hourly Settings</legend>
      <label for="select_hourly_freq">Every</label>
      <select size="1" id="select_hourly_freq"> <?php Get_Select_Options("1","12",$myrow['audit_schd_hr_frq_hr']); ?> </select>&nbsp;&nbsp;
      <b>hours</b>
      <br>
      <label for="select_hourly_start">Start the task</label>
      <select size="1" id="select_hourly_start"><?php Get_Select_Options("0","59",$myrow['audit_schd_hr_frq_min']); ?></select>&nbsp;&nbsp;
      <b>minutes past the hour</b>
      <br>
      <label for="select_hourly_start">Between a certain time</label>
      <input type="checkbox" size="20" id="check_hours_between" <?php if ( $myrow['audit_schd_hr_between'] == 1 ) { echo "CHECKED"; } ?> onClick="BetweenHours(this)"/>
      <br><br>
      <label for="select_hstrt_hour">Starting Time</label>
      <select size="1" id="select_hstrt_hour">
        <?php Get_Select_Options("0","23",$myrow['audit_schd_hr_strt_hr']); ?>
      </select>:
      <select size="1" id="select_hstrt_min" onChange="MinCopy(this)">
        <?php Get_Select_Options("0","59",$myrow['audit_schd_hr_strt_min']); ?>
      </select>
      <br>
      <label for="select_hend_hour">Ending Time</label>
      <select size="1" id="select_hend_hour">
        <?php Get_Select_Options("0","23",$myrow['audit_schd_hr_end_hr']); ?>
      </select>:
      <select size="1" id="select_hend_min">
        <?php Get_Select_Options("0","59",$myrow['audit_schd_hr_strt_min']); ?>
      </select>
    </fieldset>
    <fieldset id="fs_daily"><legend>Daily Settings</legend>
      <label for="input_days_freq">Every</label>
      <input type="text" size="3" id="input_days_freq" value="<?php echo $dly_frq ?>"/>
      &nbsp;&nbsp;<b>day(s)</b>
    </fieldset>
    <fieldset id="fs_crontab"><legend>Cron Entry</legend>
      <label for="input_cron_line">Cron Line</label>
      <input type="text" size="25" id="input_cron_line" value="<?php echo $myrow['audit_schd_cron_line'] ?>"/>
      <br>
      <label>Check Next Execution Time</label>
      <input type="button" id="cron_button" value="Cron Test" onclick="testCron(this)">
      <br><br>
      <label></label>
      <span id="cron_result"></span>
    </fieldset>
    <fieldset id="fs_weekly"><legend>Weekly Settings</legend>
      <label>On these days...</label>
      <br>
      <br>
      <table border="0" id="table_weekly" class="form-table">
        <tr><td><input type="checkbox" name="check_weekly" value="mon" <? if ( preg_match("/.*mon.*/", $myrow['audit_schd_wk_days']) ) { echo "CHECKED"; } ?> />Monday</td>
            <td><input type="checkbox" name="check_weekly" value="tue" <? if ( preg_match("/.*tue.*/", $myrow['audit_schd_wk_days']) ) { echo "CHECKED"; } ?> />Tuesday</td>
            <td><input type="checkbox" name="check_weekly" value="wed" <? if ( preg_match("/.*wed.*/", $myrow['audit_schd_wk_days']) ) { echo "CHECKED"; } ?> />Wednesday</td>
            <td><input type="checkbox" name="check_weekly" value="thu" <? if ( preg_match("/.*thu.*/", $myrow['audit_schd_wk_days']) ) { echo "CHECKED"; } ?> />Thursday</td>
        </tr><tr>
            <td><input type="checkbox" name="check_weekly" value="fri" <? if ( preg_match("/.*fri.*/", $myrow['audit_schd_wk_days']) ) { echo "CHECKED"; } ?> />Friday</td>
            <td><input type="checkbox" name="check_weekly" value="sat" <? if ( preg_match("/.*sat.*/", $myrow['audit_schd_wk_days']) ) { echo "CHECKED"; } ?> />Saturday</td>
            <td><input type="checkbox" name="check_weekly" value="sun" <? if ( preg_match("/.*sun.*/", $myrow['audit_schd_wk_days']) ) { echo "CHECKED"; } ?> />Sunday</td>
        </tr>
      </table>
    </fieldset>
    <fieldset id="fs_monthly"><legend>Monthly Settings</legend>
      <label for="select_monthly_day">This day of the month</label>
      <select size="1" id="select_monthly_day">
        <?php Get_Select_Options("1","31",$myrow['audit_schd_mth_day']); ?>
      </select><br><br>
      <label>On these months...</label><br>
      <br>
      <table border="0" class="form-table" id="table_monthly">
        <tr><td><input type="checkbox" name="check_monthly" value="jan" <? if ( preg_match("/.*jan.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />January</td>
            <td><input type="checkbox" name="check_monthly" value="feb" <? if ( preg_match("/.*feb.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />February</td>
            <td><input type="checkbox" name="check_monthly" value="mar" <? if ( preg_match("/.*mar.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />March</td>
            <td><input type="checkbox" name="check_monthly" value="apr" <? if ( preg_match("/.*apr.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />April</td>
        </tr><tr>
            <td><input type="checkbox" name="check_monthly" value="may" <? if ( preg_match("/.*may.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />May</td>
            <td><input type="checkbox" name="check_monthly" value="jun" <? if ( preg_match("/.*jun.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />June</td>
            <td><input type="checkbox" name="check_monthly" value="jul" <? if ( preg_match("/.*jul.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />July</td>
            <td><input type="checkbox" name="check_monthly" value="aug" <? if ( preg_match("/.*aug.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />August</td>
        </tr><tr>
            <td><input type="checkbox" name="check_monthly" value="sep" <? if ( preg_match("/.*sep.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />September</td>
            <td><input type="checkbox" name="check_monthly" value="oct" <? if ( preg_match("/.*oct.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />October</td>
            <td><input type="checkbox" name="check_monthly" value="nov" <? if ( preg_match("/.*nov.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />November</td>
            <td><input type="checkbox" name="check_monthly" value="dec" <? if ( preg_match("/.*dec.*/", $myrow['audit_schd_mth_months']) ) { echo "CHECKED"; } ?> />December</td>
        </tr>
      </table>
    </fieldset>
    <fieldset id="fs_email"><legend>Email Settings</legend>
      <label for="input_email_subject">Email Subject Line</label>
      <input type="text" size="20" value="<?php echo $myrow['audit_schd_email_subject'] ?>" id="input_email_subject"/>
      <br>
      <label for="input_email_replyto">Reply-To Email Address</label>
      <input type="text" size="20" value="<?php echo $myrow['audit_schd_email_replyto'] ?>"  id="input_email_replyto"/>
      <br>
      <label for="input_email_to">Email To</label>
      <input type="text" id="input_email_to">&nbsp;&nbsp;&nbsp; <img src="images/add.png" class="add" onclick="addToEmailList()">
      <br>
      <label></label><div id="EmailContainer"> <?php @Get_Email_List($emails); ?></div>
      <div id="clear-left"></div>
      <label for="select_email_logo">Email Header Logo</label>
        <?php get_file_list('./images/headers','select_email_logo', $myrow['audit_schd_email_logo']); ?>
      <br>
      <label for="select_tt_html">Toolkit Template HTML File</label>
        <?php get_file_list('./lib/tt','select_tt_html', $myrow['audit_schd_email_tt_html']); ?>
      <br>
      <label for="select_tt_text">Toolkit Template Text File</label>
        <?php get_file_list('./lib/tt','select_tt_text', $myrow['audit_schd_email_tt_html']); ?>
    </fieldset>
    <br>
      <div class="submit-push"></div>
      <input value="Submit" type="submit"/>
      <br>
      <span id="form_result_fail"></span>
  </form>
  <?php /* Display the below message if the schedule doesn't exist */
    } else {
      echo "No such schedule found.";
    }
  ?>
  <span id="form_result_fail"></span>
</div>
</td>
<?php include "include_right_column.php"; ?>
</body>
<script type='text/javascript'>
  //Call this down here to make sure all the elements are at least loaded first...
  window.onload = SchedType;
/* Get the next execution time of the cron line */
  function testCron(selected) {
    selected.disabled = true;
    var postStr = "cron_line=" + encodeURI( document.getElementById('input_cron_line').value ) +
                  "&type=cron";
    var phpPage = "audit_test_ajax.php";
    ajaxFunction(phpPage, postStr, cronTest);
  } 
</script>
</html>
