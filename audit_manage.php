<?php
$page = "admin";
include "include.php";
include "include_audit_functions.php";

?>
<link media="screen" rel="stylesheet" type="text/css" href="audit_manage.css"/>
<script type='text/javascript' src="javascript/audit_manage.js"></script>
<script type='text/javascript' src="javascript/ajax.js"></script>
<script type='text/javascript' src="javascript/async_alerts.js"></script>
<style type="text/css">
  .notifier {
    z-index: 100;
    margin: 0px 0px;
    padding: 8px 8px;
    border: 1px solid #4f79b3;
    background-color: #9cbede;
  }
</style>
<script type="text/javascript">
  window.onload = loadCron;
</script>
<td valign="top">
<div class="main_each">
<div id="box">
  <div class="header">Manage Schedules and Configurations</div>
  <br>
  <center><span id="form_result_success"></span></center>
  <div id="config-tables">
  <?php
    /* Check if cron settings exist */
    $sql  = "SELECT * FROM audit_cron";
    $result = @mysql_query($sql, $db);
    if (@mysql_num_rows($result) != 0) {
      ?>
      <label>Toggle Web-Schedule Status</label>
        <div id="result-holder">
          <img id="cron-img" onClick="toggleCron(this,'normal')"/>
        </div>
      <span id="manage-result"></span>
  <?php
      }
      else {
        echo "Web-Schedule settings not configured.
              Configure them <a href=\"audit_cron_settings.php\">here</a>";
      }
  ?>
  <br> <br> <br>
  <?php
    /* Check if any audit configs exist */
    $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
    $sql  = "SELECT * FROM audit_configurations";
    $result = @mysql_query($sql, $db);
    if (@mysql_num_rows($result) != 0) { Get_Manage_Configs($db); echo "<br>"; } 
      else { echo "No audit configurations found. <a href=\"audit_configuration.php\">Add one</a><br>"; }

    /* Check if any audit schedules exist */
    $sql  = "SELECT * FROM audit_schedules";
    $result = @mysql_query($sql, $db);
    if (@mysql_num_rows($result) != 0) { Get_Manage_Schedules($db); echo "<br>"; }
      else { echo "No audit schedules found. <a href=\"audit_schedule.php\">Add one</a><br>"; }
  ?>
  </div>
  <div id="log-box"><center><br><a href="list.php?view=cron_log">Web-Schedule Log</a></center><br><span id="log"></span></div>
  <div id="clear-left"></div>
</div>
<br>
</div>
</td>
<?php include "include_right_column.php"; ?>
</body>
</html>
