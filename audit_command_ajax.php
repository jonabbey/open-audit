<?php
include "include_config.php";
include "include_audit_functions.php";

function Update_Commands($db,$mysql_database) {
  mysql_select_db($mysql_database);
  $cmd_ids = array();
  $new_ids = array();

  /* Delete any commands that were removed */
  if ( isset($_POST['del_cmd']) ) {
    foreach ( $_POST['del_cmd'] as $del_id ) {
      mysql_query("DELETE FROM `audit_commands` WHERE audit_cmd_id='$del_id'") or 
        die("Cannot delete from DB: " . mysql_error() . "<br>");
    }
  }
  /* Add new commands */
  if ( isset($_POST['cmd_add_name']) ) {
    $count = 0;
    foreach ( $_POST['cmd_add_name'] as $name ) {
      mysql_query("INSERT INTO `audit_commands` (audit_cmd_name, audit_cmd_command) VALUES ( '$name','{$_POST['cmd_add_cmd'][$count]}')") or 
        die("Cannot add command $name: " . mysql_error() . "<br>");
      array_push($cmd_ids,mysql_insert_id());
      array_push($new_ids,mysql_insert_id());
      $count++;
    }
  }
  /* Update any commands */
  if ( isset($_POST['cmd_mod_id']) ) {
    $count = 0;
    foreach ( $_POST['cmd_mod_id'] as $id ) {
      $sql = "UPDATE `audit_commands` SET
                audit_cmd_name='{$_POST['cmd_mod_name'][$count]}',
                audit_cmd_command='{$_POST['cmd_mod_cmd'][$count]}'
              WHERE audit_cmd_id='$id'";
      mysql_query($sql) or
        die("Cannot update command {$_POST['cmd_mod_name'][$count]}: " . mysql_error() . "<br>");
      array_push($cmd_ids,$id);
      $count++;
    }
  }

  echo "<img src=\"images/button_success.png\"/><strong>The commands have been updated</strong>";
}

$action = $_POST['action'];
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password);

switch ($action) {
  case "update":
    Update_Commands($db,$mysql_database);
    break;
}

?>
