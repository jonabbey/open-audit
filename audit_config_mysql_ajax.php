<?php
include "include_config.php";
include "include_functions.php";
include "include_audit_functions.php";

function Add_Audit_Mysql($db,$database) {
  mysql_select_db($mysql_database);

  $table  = $_POST['table'];
  $field  = $_POST['field'];
  $data   = $_POST['data'];
  $sort   = $_POST['sort'];
  $id     = $_POST['id'];

  if ( $id == 'new' ) {
    $sql = "INSERT INTO audit_mysql_query ( audit_mysql_table, audit_mysql_field,
                                            audit_mysql_data , audit_mysql_sort   
            VALUES ( '{$table}','{$field}','{$data}','{$sort}' )";
  }
  else {
    $sql  = "UPDATE `audit_mysql_query` 
             SET audit_mysql_field = '{$field}', audit_mysql_table = '{$table}',
                 audit_mysql_data = '{$data}' , audit_mysql_sort = '{$sort}'
             WHERE audit_mysql_id = '{$id}'";
  }

  mysql_query($sql) or die("Could not add mysql query options: " . mysql_error() . "<br>");
  $form_action = ( $_POST['form_action'] == "edit" ) ? 'updated' : 'added';
}

$table  = $_POST['table'];
$action = $_POST['action'];
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password);

switch ($action) {
  case "get_fields":
    mysql_select_db($mysql_database);
    $field_id = ( isset($_POST['field_id']) ) ? $_POST['field_id'] : "fields_{$table}";
    $select = "<select class=\"mysql\" id=\"{$field_id}\">";
    if ( ! isset($_POST['add_query_row']) ) {
      $select .= "<option value=\"nothing\" SELECTED>Select Field</option>
                 <option value=\"nothing\">-------</option>";
    }
    if ( $table != "nothing" ) {
      $fields = Get_MySQL_Fields($db,$table);
      foreach ( $fields as $field ) {
        $select .= "<option value=\"{$field}\">{$field}</option>";
      }
    }
    $select .= "</select>";
    echo $select;
    break;
  case "add_query":
    Add_Audit_Mysql($db,$mysql_database);
    break;
}

?>
