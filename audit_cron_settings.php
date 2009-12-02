<?php
  $page = "admin";
  include "include.php";
  include "include_config.php";
  include "include_audit_functions.php";

  $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
  mysql_select_db($mysql_database);

  /* Get all data first */
  $sql     = "SELECT * FROM audit_cron";
  $result  = mysql_query($sql, $db);
  $myrow   = @mysql_fetch_array($result);

  $smtp_auth    = $myrow['audit_cron_smtp_auth'];
  $smtp_server  = ( empty($myrow['audit_cron_smtp_server'])  ) ? 'servername' : $myrow['audit_cron_smtp_server'];
  $smtp_port    = ( empty($myrow['audit_cron_smtp_port'])    ) ? '25'         : $myrow['audit_cron_smtp_port'];
  $smtp_from    = ( empty($myrow['audit_cron_smtp_from'])    ) ? 'Open-AudIT' : $myrow['audit_cron_smtp_from'];
  $service      = ( empty($myrow['audit_cron_service_name']) ) ? 'openaudit'  : $myrow['audit_cron_service_name'];
  $interval     = ( empty($myrow['audit_cron_interval'])     ) ? '5'          : $myrow['audit_cron_interval'];
  $web_address  = 
    ( empty($myrow['audit_cron_web_address']) ) ?
    preg_replace("/audit_cron_settings.php$/","","http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) :
    $myrow['audit_cron_web_address'];

  $service_enable = ( empty($myrow['audit_cron_service_enable']) ) ? '0' : $myrow['audit_cron_service_enable'];

  /* Get the encryped data now */
  $aes_key = GetAesKey();
  $sql     = "SELECT AES_DECRYPT(audit_cron_smtp_user ,'".$aes_key."' ) AS  smtp_user,
                     AES_DECRYPT(audit_cron_smtp_pass ,'".$aes_key."' ) AS  smtp_pass
              FROM audit_cron LIMIT 1";
  $auth_result = mysql_query($sql, $db);
  $myrow       = @mysql_fetch_array($auth_result);

  $smtp_user = ( empty($myrow['smtp_user']) ) ? 'username' : $myrow['smtp_user'];
  $smtp_pass = ( empty($myrow['smtp_pass']) ) ? 'password' : $myrow['smtp_pass'];
?>
<link media="screen" rel="stylesheet" type="text/css" href="audit_config.css"/>
<script type='text/javascript' src="javascript/ajax.js"></script>
<script type='text/javascript' src="javascript/audit_config.js"></script>
<script type='text/javascript'>
  window.onload = settingsOnload;
</script>
<style type="text/css">
  <?php if ( ! Windows_Server() ) { ?>
     #fs_win { display:none; }
  <?php } ?>
</style>
<td valign="top">
<div class="main_each">
<div id="box">
  <div class="submit-push">&nbsp;</div>
  <div class="header">Configure Web-Schedule Settings</div>
  <br><br>
  <form action="javascript:submitCronSettings();" method="post" id="form_config">
    <fieldset id="fs_general"><legend>General Settings</legend>
      <label>Base Web Address (URLs in emails)</label>
      <input type="text" size="30" value="<?php echo $web_address ?>" id="input_web_address"/>
      <br/>
      <label>Polling Interval (seconds)</label>
      <input type="text" size="5" value="<?php echo $interval ?>" id="input_interval"/>
    </fieldset>
    <fieldset id="fs_win"><legend>Windows Server Settings</legend>
      <label>Manage as a Service</label>
      <input type="checkbox" onclick="toggleService()" size="20" id="check_service_enable" <?php if ( $service_enable == 1 ) { echo "CHECKED"; } ?>/>
      <br/><br/>
      <label>Windows Service Name</label>
      <input type="text" size="20" value="<?php echo $service ?>" id="input_service"/>
    </fieldset>
    <fieldset id="fs_smtp"><legend>SMTP Settings</legend>
      <label>Server Name/IP</label>
      <input type="text" size="20" value="<?php echo $smtp_server ?>" id="input_smtp_server"/>
      <br/>
      <label>Port</label>
      <input type="text" size="5" value="<?php echo $smtp_port ?>" id="input_smtp_port"/>
      <br/>
      <label>From Address/Name</label>
      <input type="text" size="20" value="<?php echo $smtp_from ?>" id="input_smtp_from"/>
      <br/>
      <label>Enable SMTP Authentication</label>
      <input type="checkbox" onclick="toggleSmtpAuth(this)" size="20" id="check_smtp_auth" <?php if ( $smtp_auth == 1 ) { echo "CHECKED"; } ?>/>
      <br><br>
      <label>Username</label>
      <input type="text" size="20" value="<?php echo $smtp_user ?>" id="input_smtp_user"/>
      <br/>
      <label>Password</label>
      <input type="password" size="20" value="<?php echo $smtp_pass ?>" id="input_smtp_pass"/>
      <br/>
      <label>Send Test Message To</label>
      <input type="text" size="20" id="test_email"> <input type="button" id="smtp_button" value="send!" onclick="testSMTP(this)"> (Submit before testing)
      <br/><br/>
      <label></label>
      <span id="smtp_result"></span>
    </fieldset>
      <div class="submit-push"></div>
      <input value="Submit" type="submit"/>
      <br/>
     <span id="form_result_settings"></span>
    </form>
    <br>
</div>
<?php include "include_right_column.php"; ?>

<script type='text/javascript'>
/* Tests the LDAP settings, shows what works with a popup */
function testSMTP(selected) {
    selected.disabled = true;
    var postStr = "email=" + encodeURI( document.getElementById('test_email').value ) +
                  "&type=smtp";
    var phpPage = "audit_test_ajax.php";
    ajaxFunction(phpPage, postStr, SMTPTest);
} 
</script>
</body>
</html>
