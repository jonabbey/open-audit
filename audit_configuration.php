<?php 
  $page = "admin";
  include "include.php";
  include "include_audit_functions.php";

  $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);

  /* Get all data first */
  $sql        = "SELECT * FROM audit_configurations WHERE `audit_cfg_id` = '".$_GET['config_id']."'";
  $result     = mysql_query($sql, $db);
  $myrow      = @mysql_fetch_array($result);

  $type       = $myrow['audit_cfg_type'];
  $action     = $myrow['audit_cfg_action'];
  $os         = $myrow['audit_cfg_os'];
  $ldap_conn  = $myrow['audit_cfg_ldap_use_conn'];
  $audit_conn = $myrow['audit_cfg_audit_use_conn'];
  $ip         = explode(".", $myrow['audit_cfg_ip_start'] );
  $cmd_list   = $myrow['audit_cfg_cmd_list'];
  $mysql_ids  = $myrow['audit_cfg_mysql_ids'];
  $uuid_type  = $myrow['audit_cfg_win_uuid'];

  /* Set some defaults if we aren't editing a configuration */
  $opt_logging = ( isset($myrow['audit_cfg_log_enable'])   ) ?  $myrow['audit_cfg_log_enable']   : '1';
  $opt_tcpsyn  = ( isset($myrow['audit_cfg_nmap_tcp_syn']) ) ?  $myrow['audit_cfg_nmap_tcp_syn'] : '1';
  $opt_udp     = ( isset($myrow['audit_cfg_nmap_udp'])     ) ?  $myrow['audit_cfg_nmap_udp']     : '1';
  $opt_service = ( isset($myrow['audit_cfg_nmap_srv'])     ) ?  $myrow['audit_cfg_nmap_srv']     : '1';
  $opt_winsoft = ( isset($myrow['audit_cfg_win_sft'])      ) ?  $myrow['audit_cfg_win_sft']      : '1';

  $opt_wait = ( isset($myrow['audit_cfg_wait_time'])  ) ? $myrow['audit_cfg_wait_time'] / 60 : '10';
  $opt_max  = ( isset($myrow['audit_cfg_max_audits']) ) ? $myrow['audit_cfg_max_audits']     : '10';

  $ldap_srv  = ( isset($_GET['config_id']) ) ? $myrow['audit_cfg_ldap_server'] : 'hostname';
  $ldap_path = ( isset($_GET['config_id']) ) ? $myrow['audit_cfg_ldap_path']   : 'DC=mydomain,DC=com';
  $ldap_page = ( isset($_GET['config_id']) ) ? $myrow['audit_cfg_ldap_page']   : '1000';

  $vbs_path  = ( isset($_GET['config_id']) ) ? $myrow['audit_cfg_win_vbs'] : '//server/share/audit.vbs';

  $submit_url_path =
    preg_replace(
      "/audit_configuration.php$/",
      "admin_pc_add_2.php",
      "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
    ) ;

  $linux_url   = ( isset($_GET['config_id']) ) ? $myrow['audit_cfg_lin_url'] : $submit_url_path ;
  $windows_url = ( isset($_GET['config_id']) ) ? $myrow['audit_cfg_win_url'] : $submit_url_path ;

  $nmap_url =
    ( isset($_GET['config_id']) ) ?
    $myrow['audit_cfg_nmap_url']  :
    preg_replace(
      "/audit_configuration.php$/",
      "admin_nmap_input.php",
      "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
      ) ;

  /* Get the encryped data now */
  $aes_key = GetAesKey();
  $sql     = "SELECT AES_DECRYPT(audit_cfg_ldap_user ,'".$aes_key."' ) AS  ldap_user,
                     AES_DECRYPT(audit_cfg_ldap_pass ,'".$aes_key."' ) AS  ldap_pass,
                     AES_DECRYPT(audit_cfg_audit_user,'".$aes_key."' ) AS audit_user,
                     AES_DECRYPT(audit_cfg_audit_pass,'".$aes_key."' ) AS audit_pass
              FROM audit_configurations WHERE `audit_cfg_id` = '".$_GET['config_id']."'";
  $auth_result = mysql_query($sql, $db);
  $auth_row    = @mysql_fetch_array($auth_result);

  /* More defaults ... */
  $ldap_user  = ( isset($_GET['config_id']) ) ? $auth_row['ldap_user']  : 'user@domain.com';
  $ldap_pass  = ( isset($_GET['config_id']) ) ? $auth_row['ldap_pass']  : 'password';
  $audit_user = ( isset($_GET['config_id']) ) ? $auth_row['audit_user'] : 'user@domain.com';
  $audit_pass = ( isset($_GET['config_id']) ) ? $auth_row['audit_pass'] : 'password';

  $form_action = ( isset($_GET['config_id']) ) ? 'edit' : 'add' ; 

  $head = 
    ( isset($_GET['config_id']) ) ?
    "Editing Configuration \"{$myrow['audit_cfg_name']}\"" :
    'Add a Configuration';

  $sql  = "SELECT * FROM ldap_connections";
  $l_result = mysql_query($sql, $db);
  $ldap_connections = (mysql_num_rows($l_result) != 0) ? '1' : '0';
?>
<link media="screen" rel="stylesheet" type="text/css" href="audit_config.css"/>
<script type='text/javascript' src="javascript/audit_config.js"></script>
<script type='text/javascript' src="javascript/audit_mysql_query.js"></script>
<script type='text/javascript' src="javascript/async_alerts.js"></script>
<td valign="top">
<div class="main_each">
  <div class="form-result"><span id="form_result_success"></span></div>
  <?php
  $sql        = "SELECT * FROM audit_cron";
  $c_result     = mysql_query($sql, $db);

  /* Make sure they set the cron settings first */
  if (@mysql_num_rows($c_result) != 0) {
    /* Check if the config exists. Do not show the form if none exists */
    $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
    $sql  = "SELECT * FROM audit_configurations WHERE audit_cfg_id = '".$_GET['config_id']."'";
    $result = @mysql_query($sql, $db);
    if (@mysql_num_rows($result) != 0 or !isset($_GET['config_id']) ) {
  ?>
  <div class="submit-push">&nbsp;</div>
  <div class="header"><?php echo $head ?></div>
  <br><br>
  <form action="javascript:get('config','<?php echo $form_action ?>','<?php echo $_GET['config_id']; ?>');" method="post" id="form_config">
    <fieldset><legend>General Settings</legend>
      <label>Name</label>
        <input type="text" size="20" id="input_name" value="<?php echo $myrow['audit_cfg_name']; ?>"/>
      <br>
      <label>Audit Action</label>
        <select size="1" onChange="SwitchAction(this)" id="select_action">
          <option value="nothing">Select Audit Action</option>
          <option value="nothing">-------</option>
          <option value="pc" <?php if ( $action == "pc" ) { echo "SELECTED"; } ?> >Computer Audit</option>
          <option value="nmap" <?php if ( $action == "nmap" ) { echo "SELECTED"; } ?> >Port Scan (NMAP)</option>
          <option value="pc_nmap" <?php if ( $action == "pc_nmap" ) { echo "SELECTED"; } ?> >Computer Audit and Port Scan</option>
          <option value="command"<?php if ( $action == "command" ) { echo "SELECTED"; } ?> >Remote Command</option>
        </select>
      <br>
      <label>Audit Type</label>
        <select size="1" onChange="SwitchConfig(this)" id="select_audit">
          <option value="nothing">Select Audit Type</option>
          <option value="nothing">-------</option>
          <option value="list" <?php if ( $type == "list" ) { echo "SELECTED"; } ?> >Computer List</option>
          <option value="domain" <?php if ( $type == "domain" ) { echo "SELECTED"; } ?> >Domain</option>
          <option value="iprange" <?php if ( $type == "iprange" ) { echo "SELECTED"; } ?> >IP Range</option>
          <option value="mysql" <?php if ( $type == "mysql" ) { echo "SELECTED"; } ?> >MySQL Query</option>
        </select>
      <br>
      <label>OS Type</label>
        <select size="1" onChange="SwitchOS(this)" id="select_os" class="pc command">
          <option value="nothing">Select OS Type</option>
          <option value="nothing">-------</option>
          <option value="windows" <?php if ( $os == "windows" ) { echo "SELECTED"; } ?> >Windows</option>
          <option value="linux" <?php if ( $os == "linux" ) { echo "SELECTED"; } ?> >Linux</option>
        </select>
      <br>
      <label>Simultaneous Audits</label>
        <input type="text" size="1" value="<?php echo $opt_max ?>" id="input_max_audits"/><br>
      <label>Kill scripts running longer than</label>
        <input type="text" size="1" value="<?php echo $opt_wait ?>" id="input_wait_time"/>&nbsp;&nbsp;<strong>minutes</strong>
      <br>
      <label>Enable Logging</label>
       <input type="checkbox" size="20" id="check_log_enable" onclick="ToggleLogging(this)" <?php if ( $opt_logging == 1 ) { echo "CHECKED"; } ?>/>
     <br>
    </fieldset>
    <fieldset id="fs_auth" class="pc command"><legend>Audit Credentials</legend>
      <?php 
        if ( $ldap_connections == '1' ) {
          echo "<label>Use LDAP Connection</label>";
        }
        else {
          echo "<label>No LDAP Connections Found</label>";
        }

        $select = "select_audit_cred";
        $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
        Get_LDAP_Connections($select,$db,$myrow['audit_cfg_audit_conn']); ?>
      <?php 
        if ( $ldap_connections == '1' ) {
          echo "<br><br>";
          echo "<label>Or Manually Enter Credentials...</label>";
        }
        else {
          echo "<br>";
          echo "<label>Manually Enter Credentials</label>";
        }
      ?>
      <br><br>
      <br>
      <label>Username</label>
        <input type="text" id="input_cred_user" size="20" value="<?php echo $audit_user ?>"/>
      <br>
      <label>Password</label>
        <input type="password" id="input_cred_pass" size="20" value="<?php echo $audit_pass ?>"/>
      <br>
      <label>This is a local account</label>
        <input type="checkbox" size="20" id="check_cred_local" <?php if ( $myrow['audit_cfg_audit_local'] == 1 ) { echo "CHECKED"; } ?>/>
      <br><br>
    </fieldset>
    <fieldset id="fs_nmap" class="nmap"><legend>NMAP Settings</legend>
      <label>TCP SYN Scan</label>
        <input type="checkbox" size="20" id="check_nmap_tcp_syn" <?php if ( $opt_tcpsyn == 1 ) { echo "CHECKED"; } ?>/>
      <br><br>
      <label>UDP Scan</label>
        <input type="checkbox" size="20" id="check_nmap_udp" <?php if ( $opt_udp == 1 ) { echo "CHECKED"; } ?>/>
      <br><br>
      <label>Service Version Detection</label>
        <input type="checkbox" size="20" id="check_nmap_srv" <?php if ( $opt_service == 1 ) { echo "CHECKED"; } ?>/>
      <br><br>
      <label>Intensity</label>
      <select size="1" id="select_nmap_intensity">
          <option value="0" <?php if ( $myrow['audit_cfg_nmap_int'] == 0 ) { echo "SELECTED"; } ?> >0 - Less Accurate, Shorter</option>
          <option value="1" <?php if ( $myrow['audit_cfg_nmap_int'] == 1 ) { echo "SELECTED"; } ?> >1</option>
          <option value="2" <?php if ( $myrow['audit_cfg_nmap_int'] == 2 ) { echo "SELECTED"; } ?> >2</option>
          <option value="3" <?php if ( $myrow['audit_cfg_nmap_int'] == 3 ) { echo "SELECTED"; } ?> >3</option>
          <option value="4" <?php if ( $myrow['audit_cfg_nmap_int'] == 4 ) { echo "SELECTED"; } ?> >4</option>
          <option value="5" <?php if ( $myrow['audit_cfg_nmap_int'] == 5 ) { echo "SELECTED"; } ?> >5</option>
          <option value="6" <?php if ( $myrow['audit_cfg_nmap_int'] == 6 ) { echo "SELECTED"; } ?> >6</option>
          <option value="7" <?php if ( $myrow['audit_cfg_nmap_int'] == 7 || !isset($_GET['config_id']) ) { echo "SELECTED"; } ?> >7 - Recommended</option>
          <option value="8" <?php if ( $myrow['audit_cfg_nmap_int'] == 8 ) { echo "SELECTED"; } ?> >8</option>
          <option value="9" <?php if ( $myrow['audit_cfg_nmap_int'] == 9 ) { echo "SELECTED"; } ?> >9 - Most Accurate, Very Long</option>
        </select>
      <br>
      <label>NMAP Path (Optional)</label>
        <input type="text" size="20" value="<?php echo $myrow['audit_cfg_nmap_path'] ?>" id="input_nmap_path"/>
      <br>
      <label>Sumbit Results To</label>
        <input type="text" size="30" value="<?php echo $nmap_url ?>" id="input_nmap_url"/>
      <br>
      <?php
        /* Only show tests if editing the page */ 
        if ( isset($_GET['config_id']) ) { ?>
        <label><input value="Test NMAP" id="test_nmap" type="button" onclick="openNmapPopup(this)" /></label>
        <div id="nmap_result"><br><br>Save First!</div>
      <?php } ?>
    </fieldset>
    <fieldset id="fs_command" class="command"><legend>Remote Command</legend>
      <label>Desktop Interaction (Windows only)</label>
        <input type="checkbox" size="20" id="check_command_interact" <?php if ( $myrow['audit_cfg_command_interact'] == 1 ) { echo "CHECKED"; } ?>/>
      <br><br>
      <br>
      <center><strong>List of commands to run. One command per line.</strong></center>
      <br> <br>
      <center><textarea cols="50" rows="10" id="text_commands"><?php echo $myrow['audit_cfg_command_list']; ?></textarea></center><br><br>
      <br> 
      <center><strong>Run these commands. In order from top to bottom.</center></strong>
      <br>
      <label></label>
    <?php 
      $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
      $sql    = "SELECT * FROM audit_commands";
      $result = @mysql_query($sql, $db);
      if ( @mysql_num_rows($result) != 0 ) {
        echo "<div id=\"commandContainer\">
                <div id=\"sortButtons\">
                  <div><img src=\"images/up.png\" onclick=\"boxUp()\" class=\"sort\"/></div>
                  <div><img src=\"images/down.png\" onclick=\"boxDown()\" class=\"sort\"/></div>
                </div>";
        echo "<div id=\"DragContainer\">";
                @Get_Commands($db,$cmd_list);
        echo "</div>";
      }
      else {
        echo "<b>No commands found in DB. Add some <a href=\"audit_commands.php\">here</a></b>.";
      }
    ?>
      <br>
    </fieldset>
    <fieldset id="fs_ldap" class="domain"><legend>LDAP Settings</legend>
      <?php 
        if ( $ldap_connections == '1' ) {
          echo "<label>Use LDAP Connection</label>";
        }
        else {
          echo "<label>No LDAP Connections Found</label>";
        }

        $select = "select_ldap_cred";
        $db = mysql_connect($mysql_server,$mysql_user,$mysql_password);
        Get_LDAP_Connections($select,$db,$myrow['audit_cfg_ldap_conn']); ?>
      <?php 
        if ( $ldap_connections == '1' ) {
          echo "<br><br>";
          echo "<label>Or Manually Enter Credentials...</label>";
        }
        else {
          echo "<br>";
          echo "<label>Manually Enter Credentials</label>";
        }
      ?>
      <br><br><br>
      <label>Username</label>
        <input type="text" size="20" value="<?php echo $ldap_user ?>" id="input_ldap_user"/>
      <br>
      <label>Password</label>
        <input type="password" size="20" id="input_ldap_pass" value="<?php echo $ldap_pass ?>"/>
      <br>
      <label>LDAP Server</label>
        <input type="text" size="20" value="<?php echo $ldap_srv ?>" id="input_ldap_server"/>
      <br>
      <label>LDAP Path</label>
        <input type="text" size="20" value="<?php echo $ldap_path ?>" id="input_ldap_path"/>
      <br>
      <label>LDAP Page Size</label>
        <input type="text" size="20" value="<?php echo $ldap_page ?>" id="input_ldap_page"/>
      <br><br>
      <label><i>Query Filter...</i></label><br>
      <br><br>
      <label>Perl Regex Filter</label>
        <input type="text" size="20" value="<?php echo $myrow['audit_cfg_filter'] ?>" id="input_filter" name="ldap_filter"/>
      <br>
      <label>Case Insensitive</label>
        <input type="checkbox" size="20" id="check_filter_case" name="ldap_filter" <?php if ( $myrow['audit_cfg_filter_case'] == 1 ) { echo "CHECKED"; } ?> />
      <br><br>
      <label>Non-Matching Results Only</label>
        <input type="checkbox" size="20" id="check_filter_inverse" name="ldap_filter" <?php if ( $myrow['audit_cfg_filter_inverse'] == 1 ) { echo "CHECKED"; } ?> />
      <br><br>
      <?php
        /* Only show tests if editing the page */ 
        if ( isset($_GET['config_id']) ) { ?>
        <label><input value="Test LDAP" id="test_ldap" type="button" onclick="openLDAPPopup(this)" /></label>
        <div id="ldap_result"><br><br>Save first!</div>
      <?php } ?>
    </fieldset>
    <fieldset id="fs_list"><legend>Computer List</legend>
     <center><strong>A list of computers, one computer per line</strong></center>
       <br>
     <center><textarea cols="50" rows="10" id="text_pc_list"><?php echo $myrow['audit_cfg_pc_list']; ?></textarea></center>
    </fieldset>
    <fieldset id="fs_range" class="range"><legend>IP Range</legend>
      <label>IP Start</label>
        <input type="text" id="start_ip_1" size="2" maxlength="3" value="<?php echo $ip[0]; ?>" onChange="IpCopy(this,'1')"/>&nbsp;.&nbsp;<input type="text" id="start_ip_2" size="2" value="<?php echo $ip[1]; ?>" onChange="IpCopy(this,2)"/>&nbsp;.&nbsp;<input type="text" id="start_ip_3" size="2" value="<?php echo $ip[2]; ?>" onChange="IpCopy(this,3)"/>&nbsp;.&nbsp;<input type="text" id="start_ip_4" size="2" value="<?php echo $ip[3]; ?>" />
      <br>
      <label>IP End</label>
        <input type="text" id="end_ip_1" value="<?php echo $ip[0]; ?>" size="2"/>&nbsp;.&nbsp;<input type="text" id="end_ip_2" size="2" value="<?php echo $ip[1]; ?>" />&nbsp;.&nbsp;<input type="text" id="end_ip_3" size="2" value="<?php echo $ip[2]; ?>" />&nbsp;.&nbsp;<input type="text" id="end_ip_4" size="2" value="<?php if ( $myrow['audit_cfg_ip_end'] != 0 ) { echo $myrow['audit_cfg_ip_end']; } ?>" />
      <br>
    </fieldset>
    <fieldset id="fs_mysql"><legend>MySQL Query</legend>
      <?php 
        /* Show the tables */
        $tables = array(
                    'network_card' , 'scheduled_task' ,
                    'software' , 'system', 'motherboard',
                    'processor', 'service', 'sound', 'usb', 'video'
                  );
        echo "<label>Table</label>";
        echo "<select class=\"mysql\" id=\"mysql_tables\" onChange=\"setMysqlFields(this,'select_fields')\">
                <option value=\"nothing\" SELECTED>Select MySQL Table</option>
                <option value=\"nothing\">-------</option>";
        foreach ( $tables as $table ) {
           echo "<option value=\"{$table}\">{$table}</option>";
        }
        echo "</select>
              <br>
              <label>Field</label>";
        /* Show fields in the table */
        echo "<div id=\"select_fields\">
                <select class=\"mysql\" id=\"fields_nothing\">
                <option value=\"nothing\" SELECTED>Select Field</option>
                <option value=\"nothing\">-------</option>";
        echo "  </select>
              </div>
              <label>Search Method</label>
              <select class=\"mysql\" id=\"fields_sort\">
                <option value=\"contains\" SELECTED>Contains</option>
                <option value=\"begins\">Begins With</option>
                <option value=\"ends\">Ends With</option>
                <option value=\"equals\">Equals</option>
                <option value=\"notequal\">Does Not Equal</option>
                <option value=\"notcontain\">Does Not Contain</option>
              </select>
              <br>
              <label>Search for Data</label>
              <input class=\"mysql\" size=\"15\" type=\"text\" id=\"input_field_value\">
              <img src=\"images/add.png\" class=\"addbutton\" onclick=\"addToQuery()\">
              <br>";
     ?>
      <br>
      <center>
      <table id="mysql_query_options">
        <?php Get_MySQL_Queries($db,$_GET['config_id']); ?>
      </table>
      </center>
      <br><br>
      <label><i>Query Filter...</i></label><br>
      <br><br>
      <label>Perl Regex Filter</label>
        <input type="text" size="20" value="<?php echo $myrow['audit_cfg_filter'] ?>" id="input_filter" name="mysql_filter"/>
      <br>
      <label>Case Insensitive</label>
        <input type="checkbox" size="20" id="check_filter_case" name="mysql_filter" <?php if ( $myrow['audit_cfg_filter_case'] == 1 ) { echo "CHECKED"; } ?> />
      <br><br>
      <label>Non-Matching Results Only</label>
        <input type="checkbox" size="20" id="check_filter_inverse" name="mysql_filter" <?php if ( $myrow['audit_cfg_filter_inverse'] == 1 ) { echo "CHECKED"; } ?> />
      <br><br>
      <?php
        /* Only show tests if editing the page */ 
        if ( isset($_GET['config_id']) ) { ?>
        <label> <input value="Test MySQL" id="test_mysql" type="button" onclick="openMysqlPopup(this)" /></label>
        <div id="mysql_result"><br><br>Save first!</div>
      <?php } ?>

    </fieldset>
    <fieldset id="fs_windows" class="pc command"><legend>Windows Audit Settings</legend>
      <label>Audit.vbs UNC Path</label>
        <input type="text" size="30" value="<?php echo $vbs_path ?>" id="input_vbs"/>
      <br>
      <label>Submit Results To</label>
        <input type="text" size="30" value="<?php echo $windows_url ?>" id="input_windows_url"/>
      <br>
      <label>Winexe/RemCom.exe Path (Optional)</label>
        <input type="text" size="30" value="<?php echo $myrow['audit_cfg_com_path'] ?>" id="input_com_path"/>
      <br>
      <label>UUID Type</label>
        <select id="select_windows_uuid">
          <option value="uuid" <?php if ( $uuid_type == "uuid" or empty($uuid) ) { echo "SELECTED"; } ?> >UUID</option>
          <option value="mac"  <?php if ( $uuid_type == "mac"  ) { echo "SELECTED"; } ?> >MAC Address</option>
          <option value="name" <?php if ( $uuid_type == "name" ) { echo "SELECTED"; } ?> >System Name</option>
        </select>
      <br>
      <label>Audit Software</label>
        <input type="checkbox" size="20" id="check_windows_software" <?php if ( $opt_winsoft == 1 ) { echo "CHECKED"; } ?>/>
      <br>
    </fieldset>
    <fieldset id="fs_linux" class="pc command"><legend>Linux Audit Settings</legend>
      <label>Submit Results To</label>
        <input type="text" size="30" value="<?php echo $linux_url ?>" id="input_linux_url"/>
      <br>
      <label>Audit Software</label>
        <input type="checkbox" size="20" id="check_linux_software" <?php if ( $myrow['audit_cfg_lin_sft'] == 1 ) { echo "CHECKED"; } ?>/>
      <br><br>
      <label>Only check these packages</label>
        <input type="checkbox" size="20" id="check_linux_software_list" <?php if ( $myrow['audit_cfg_lin_sft_lst'] == 1 ) { echo "CHECKED"; } ?> /><br>
      <br><br>
       <center><textarea cols="50" rows="10" id="text_linux_software"><?php echo $myrow['audit_cfg_sft_lst']; ?></textarea></center>
    </fieldset>
      <div class="submit-push"></div>
      <input value="Submit" type="submit"/>
      <br>
      <span id="form_result_fail"></span>
  </form>
      <?php /* Display the below message if the schedule doesn't exist */
            }
            else {
              echo "No such configuration found.";
            }
          }
          else {
            echo "Configure your main <a href=\"audit_cron_settings.php\">cron daemon settings</a> first.";
          }
    ?>
</div>
</td>
<script type='text/javascript'>
window.onload = ConfigType;

/* Tests the LDAP settings, shows what works with a popup */
function openLDAPPopup(selected) {
    selected.disabled = true;
    var postStr = "config_id=" + <?php echo $_GET['config_id']; ?> +
                      "&type=ldap";
    var phpPage = "audit_test_ajax.php";
    ajaxFunction(phpPage, postStr, LDAPTest);
} 

/* Tests the nmap settings, shows what works with a popup */
function openNmapPopup(selected) {
    selected.disabled = true;
    var postStr = "config_id=" + <?php echo $_GET['config_id']; ?> +
                      "&type=nmap";
    var phpPage = "audit_test_ajax.php";
    ajaxFunction(phpPage, postStr, NMAPTest);
}

function openMysqlPopup(selected) {
    selected.disabled = true;
    var postStr = "config_id=" + <?php echo $_GET['config_id']; ?> +
                      "&type=mysql";
    var phpPage = "audit_test_ajax.php";
    ajaxFunction(phpPage, postStr, MysqlTest);
} 
</script>
<?php include "include_right_column.php"; ?>
</body>
</html>
