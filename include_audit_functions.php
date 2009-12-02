<?php

function Get_Email_List($email_list) {
  $emails = array();
  $emails = explode(";",$email_list);
  $count  = 0;

  while ( $email = array_pop($emails) ) {
    $count++;
    echo "<div class=\"Box\" id=\"email$count\">
          <input type=\"hidden\" name=\"email_to\" value=\"$email\">
          <img src=\"images/delete.png\" id=\"$count\" class=\"delete\" onclick=\"removeEmail(document.getElementById('email$count'))\">&nbsp;&nbsp;$email</div>";
  }
}

function Get_Commands($db,$cmd_list) {
  $sql    = "SELECT * FROM audit_commands";
  $result = mysql_query($sql, $db);

  // Needed to stop onlick from parent div
  $js = "if (typeof event.stopPropagation != 'undefined') {
           event.stopPropagation();
         }
         if (typeof event.cancelBubble != 'undefined') {
           event.cancelBubble = true;
         }";

  // No commands in list, just show them all in any order
  if ( empty($cmd_list) ) {
    while ( $myrow = mysql_fetch_array($result) ) {
      $id      = $myrow['audit_cmd_id'];
      $name    = $myrow['audit_cmd_name'];
      $command = $myrow['audit_cmd_command'];
      $checked = ( !empty($cmd_id) AND $cmd_id == $id ) ? 'SELECTED' : '' ;
      if ( empty($name) ) { next; }
      echo "<div class=\"Box\" onclick=\"MakeMovable(this)\" id=\"$id\">
            <input type=\"checkbox\" value=\"$id\" name=\"$name\" onclick=\"$js\">$name</div>";
    }
  }
  else {  // Order the commands from the list so they appear in order
    $cmds_chk = explode(',',$cmd_list);
    foreach  ( $cmds_chk as $id ) {
      $result = mysql_query($sql, $db);
      while ( $myrow = mysql_fetch_array($result) ) {
        if ( $myrow['audit_cmd_id'] == $id ) {
          echo "<div class=\"Box\" onclick=\"MakeMovable(this)\" id=\"".$id."\">
                <input type=\"checkbox\" value=\"$id\" name=\"{$myrow['audit_cmd_name']}\" onclick=\"$js\" CHECKED>{$myrow['audit_cmd_name']}</div>";
          break;
        }
      }
    }

    $result = mysql_query($sql, $db);
    while ( $myrow = mysql_fetch_array($result) ) {
      $id = $myrow['audit_cmd_id'];
      if ( ! preg_grep("/.*$id.*/",$cmds_chk) ) {
        $name    = $myrow['audit_cmd_name'];
        $command = $myrow['audit_cmd_command'];
        if ( empty($name) ) { next; }
        echo "<div class=\"Box\" onclick=\"MakeMovable(this)\" id=\"$id\">
              <input type=\"checkbox\" value=\"$id\" name=\"$name\" onclick=\"$js\">$name</div>";
      }
    }
  }
}

function get_command_info($db){
  $sql    = "SELECT * FROM audit_commands";
  $result = mysql_query($sql, $db);
  if ( mysql_num_rows($result) != 0 ) {
    echo "<tr id=\"table-cmd-head\">
            <td><center><b>Name</b></center></td>
            <td><center><b>Command</b></center></td>
            <td><center><b>Delete</b></center></td>
          </tr>";
    $count = 0;
    while ( $myrow = mysql_fetch_array($result) ) {
      $id      = $myrow['audit_cmd_id'];
      $name    = $myrow['audit_cmd_name'];
      $command = $myrow['audit_cmd_command'];
      echo "
        <tr id=\"$id\">
          <td><input type=\"text\" value=\"".$name."\" size=\"20\" id=\"cmdname$count\"/></td>
          <td><input type=\"text\" class=\"command-value\" value=\"".$command."\" id=\"cmd$count\"/></td>
          <td><img src=\"images/delete.png\" onClick=\"removeCommand(this)\" id=\"$count\" class=\"deletebutton\"/></td>
        </tr>
      ";
      $count++;
    }
  }
}

function get_dir_files($dir) {
  $files = array();
  $fh    = opendir($dir);

  while ( $file = readdir($fh) ) {
    if ( $file != '.' && $file != '..' ) {
      $files[] = $file;
    }
  }

  closedir($fh);
  rsort($files);

  return $files;
}

function get_file_list($dir,$id,$selected) {
  $files = get_dir_files($dir);

  echo "<select id=\"{$id}\">
        <option value=\"\" SELECTED>Default</option>
        <option value=\"\">---------</option>";
  while ( $file = array_pop($files) ) {
    $default = ( $file == $selected ) ? 'SELECTED' : '' ;
    echo "<option value=\"$file\" $default>$file</option>";
  }
  echo "</select>";
}

// Get all the queries associated with a configuration, displayed on
// audit_configuration.php
function Get_MySQL_Queries($db,$cfg_id) {
  $sql      = "SELECT * FROM audit_mysql_query WHERE audit_mysql_cfg_id = '$cfg_id'";
  $result   = mysql_query($sql, $db);
  $tables = array(
    'network_card' , 'scheduled_task' , 'usb'        ,
    'software'     , 'system'         , 'motherboard',
    'processor'    , 'service'        , 'sound'      ,
    'video'
  );
  $srt_flds = array(
    'contains'   => 'Contains',
    'begins'     => 'Begins With',
    'ends'       => 'Ends With',
    'equals'     => 'Equals',
    'notequal'   => 'Does Not Equal',
    'notcontain' => 'Does Not Contain'
  );

  if ( $result ) {
    $count = 0;
    while ( $myrow = mysql_fetch_array($result) ) {
      $id    = $myrow['audit_mysql_id'];
      $field = $myrow['audit_mysql_field'];
      $table = $myrow['audit_mysql_table'];
      $data  = $myrow['audit_mysql_data'];
      $sort  = $myrow['audit_mysql_sort'];

      echo "<tr id=\"{$id}\">
              <td>
                <img src=\"images/delete.png\" id=\"$count\" class=\"deletebutton\" onclick=\"removeQueryOpt(this)\" ></td>
              <td>
                <select class=\"mysql\" id=\"qtbl$count\" onChange=\"setFieldSelect(this,'cellfield$count','qfld$count')\">";
                foreach ( $tables as $line ) {
                  $selected = ( $line == $table ) ? 'SELECTED' : '';
                  echo "<option value=\"$line\" $selected>$line</option>";
                }
      echo "    </select>
              </td>
              <td id=\"cellfield{$count}\">
               <select class=\"mysql\" id=\"qfld{$count}\">";
               $fields = Get_MySQL_Fields($db,$table);
                foreach ( $fields as $line ) {
                  $selected = ( $line == $field ) ? 'SELECTED' : '';
                  echo "<option value=\"$line\" $selected>$line</option>";
                }
      echo "    </select>
              </td>
              <td>
                <select class=\"mysql\" id=\"qsrt{$count}\">";
                foreach ( $srt_flds as $key => $value ) {
                  $selected = ( $key == $sort ) ? 'SELECTED' : '';
                  echo "<option value=\"$key\" $selected>$value</option>";
                }
      echo "    </select>
              <td><input size=\"15\" class=\"mysql\" id=\"qdata$count\" value=\"$data\"></td>
            </tr>";
      $count++;
    }
  }
}

function Get_LDAP_Connections($select_name,$db,$conn_id) {
  $sql  = "SELECT * FROM ldap_connections";
  $result = mysql_query($sql, $db);
  if (mysql_num_rows($result) != 0) {
    echo "<select size=\"1\" id=\"$select_name\" onChange=\"ToggleAuth(this)\">";
    echo "<option value=\"nothing\" selected=\"selected\">Select Connection</option>";
    echo "<option value=\"nothing\">-------</option>";
    while ( $myrow = mysql_fetch_array($result) ) {
      $name = $myrow['ldap_connections_name'];
      $select = ( !empty($conn_id) AND $conn_id == $myrow['ldap_connections_id'] ) ? 'SELECTED' : '' ;
      if ( empty($myrow['ldap_connections_name']) ) { $name = "No Name - Conn ID ".$myrow['ldap_connections_id']; }
      echo "<option value=\"".$myrow['ldap_connections_id']."\" $select>$name</option>";
      }
    echo "</select>";
  } else {
    echo "<select size=\"1\" id=\"$select_name\" STYLE=\"visibility:hidden\">";
    echo "<option value=\"nothing\" SELECTED>None Found</option>";
    echo "</select><br><br>";
  }
}

function Get_Audit_Configs($db,$config_id) {
  $sql  = "SELECT * FROM audit_configurations";
  $result = mysql_query($sql, $db);
  echo "<select size=\"1\" id=\"select_config\">";
  echo "<option value=\"nothing\" selected=\"selected\">Select Audit Config</option>";
  echo "<option value=\"nothing\">-------</option>";
  while ( $myrow = mysql_fetch_array($result) ) {
    $name = $myrow['audit_cfg_name'];
    $select = ( !empty($config_id) AND $config_id == $myrow['audit_cfg_id'] ) ? 'SELECTED' : '' ;
    echo "<option value=\"{$myrow['audit_cfg_id']}\" $select>$name</option>";
  }
  echo "</select>";
}

function Get_Select_Options($start,$end,$selected) {
  while ( $start <= $end ) {
    $value = ( preg_match("/^[0-9]$/", $start) ) ? "0".$start : $start;
    $select = ( !empty($selected) AND $selected == $start ) ? 'SELECTED' : '' ;
    echo "<option value=\"$start\" $select>$value</option>";
    $start++;
  }
}

function Get_Config_Name($db,$id) {
  $sql  = "SELECT `audit_cfg_name` FROM audit_configurations WHERE `audit_cfg_id` = '$id'";
  $result = mysql_query($sql, $db);
  $myrow = mysql_fetch_array($result);
  return $myrow['audit_cfg_name'];
}

function Get_Manage_Configs($db) {
  $sql  = "SELECT * FROM audit_configurations";
  $result = mysql_query($sql, $db);
  echo "<div id=\"cfg-holder\"><table id=\"config-table\" summary=\"Audit Configurations\">
    <thead>
    	<tr>
          <th scope=\"row\" colspan=\"5\"><center>Audit Configurations</center></th>
        </tr>
    	<tr>
          <th scope=\"col\">Name</th>
          <th scope=\"col\">Action</th>
          <th scope=\"col\">Type</th>
          <th scope=\"col\">Run</th>
          <th scope=\"col\">Delete</th>
        </tr>
    </thead>
    <tbody>";
  while ( $myrow = mysql_fetch_array($result) ) {
    $cfg_action = array(
      'pc'      => "PC Audit",
      'nmap'    => "Port Scan",
      'pc_nmap' => "Audit/Port Scan",
      'command' => "Commands"
    );
    $cfg_type = array(
      'iprange' => "IP Range",
      'domain'  => "LDAP",
      'list'    => "PC List",
      'mysql'   => "MySQL"
    );
    $audit_action = $cfg_action[$myrow['audit_cfg_action']];
    $audit_type   = $cfg_type[$myrow['audit_cfg_type']];
    echo "<tr>
            <td><a href=\"audit_configuration.php?config_id={$myrow['audit_cfg_id']}\">{$myrow['audit_cfg_name']}</a></td>
            <td>$audit_action</td>
            <td>$audit_type</td>
            <td>&nbsp;&nbsp;&nbsp;<img src=\"images/audit.png\" id=\"manage-img\"".
            " onClick=\"auditConfigNow({$myrow['audit_cfg_id']},'{$myrow['audit_cfg_name']}')\"/></td>
            <td>&nbsp;&nbsp;&nbsp;<img src=\"images/button_fail.png\" id=\"manage-img\"".
            "alt=\"Delete this Configuration\" ".
            "onClick=\"deleteConfigRow(this,{$myrow['audit_cfg_id']},'{$myrow['audit_cfg_name']}')\"/></td>
          </tr>";
  }
  echo "</tbody></table></div>";
}

function Get_Manage_Schedules($db) {
  $type_map = array(
    'hourly'  => "Hourly",
    'weekly'  => "Weekly",
    'monthly' => "Monthly",
    'daily'   => "Daily",
    'crontab' => "Cron Entry"
  );
  $sql  = "SELECT * FROM audit_schedules";
  $result = mysql_query($sql, $db);
  echo "<div id=\"sched-holder\"><table id=\"sched-table\" summary=\"Audit Schedules\">
    <thead>
    	<tr>
          <th scope=\"row\" colspan=\"7\"><center>Audit Schedules</center></th>
        </tr>
    	<tr>
          <th scope=\"col\">Name</th>
          <th scope=\"col\">Config</th>
          <th scope=\"col\">Type</th>
          <th scope=\"col\">Last Run</th>
          <th scope=\"col\">Next Run</th>
          <th scope=\"col\">Stop/Start</th>
          <th scope=\"col\">Delete</th>
        </tr>
    </thead>
    <tbody>";
  while ( $myrow = mysql_fetch_array($result) ) {
    $config_name  = Get_Config_Name($db,$myrow['audit_schd_cfg_id']);
    $status_image = ( $myrow['audit_schd_active'] == "1" ) ? ( 'start' ) : ( 'stop'  );
    $sched_type = $type_map[$myrow['audit_schd_type']];
    $run_time = ( $myrow['audit_schd_last_run'] == 0 ) ? ( 'Never' ) : ( date('D M jS Y h:i:s A',$myrow['audit_schd_last_run']) );
    $next_run = ( $myrow['audit_schd_next_run'] == 0 ) ? ( 'unknown' ) : ( date('D M jS Y h:i:s A',$myrow['audit_schd_next_run']) );
    echo "<tr>
            <td><a href=\"audit_schedule.php?sched_id={$myrow['audit_schd_id']}\">{$myrow['audit_schd_name']}</a></td>
            <td>$config_name</td>
            <td>$sched_type</td>
            <td>$run_time</td>
            <td>$next_run</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"images/$status_image.png\" id=\"manage-img\" onClick=\"toggleSchedule(this,{$myrow['audit_schd_id']},'{$myrow['audit_schd_name']}')\"/></td>
            <td>&nbsp;&nbsp;&nbsp;<img src=\"images/button_fail.png\" id=\"manage-img\" onClick=\"deleteSchedRow(this,{$myrow['audit_schd_id']},'{$myrow['audit_schd_name']}')\"/></td>
          </tr>";
  }
  echo "</tbody></table></div>";
}

/* Given the table name and the mysql connection
   return the fields for the table as an array   */
function Get_MySQL_Fields($db,$table) {
  $result = mysql_query("SHOW COLUMNS FROM $table");
  $fields = array();
  if (!$result) {
    echo 'Could not run query: ' . mysql_error();
    exit;
  }

  if (mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_array($result)) { array_push($fields,$row['Field']); };
  }

  sort($fields);

  return $fields;
}

/* If the web server is running windows, return true */
function Windows_Server() {

  if ( preg_match("/Win32|Win64|Windows|mswin|microsoft/i",$_SERVER['SERVER_SIGNATURE']) ) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

/* Make a best guess about what to execute commands with from the web interface */
function Get_Audit_Bin() {
  $wdir = getcwd();

  if ( Windows_Server() && file_exists('./scripts/audit.exe') ) {
    $bin = "$wdir\\scripts\\audit.exe";
  }
  elseif ( !Windows_Server() && file_exists('./scripts/audit') ) {
    $bin = "\"$wdir/scripts/audit\"";
  } 
  elseif ( file_exists('./scripts/audit.pl') ) {
    $bin = ( Windows_Server() ) ? "perl \"$wdir\\scripts\\audit.pl\"" : "\"$wdir/scripts/audit.pl\"";
  } 

  return $bin;
}
?>
