<?php
require_once "include_config.php";
require_once "include_functions.php";
require_once "include_audit_functions.php";

function Add_Audit_Mysql($db,$database) {
  mysql_select_db($mysql_database);

  $table  = $_POST['table'];
  $field  = $_POST['field'];
  $data   = $_POST['data'];
  $sort   = $_POST['sort'];
  $id     = $_POST['id'];

  if ( $id == 'new' ) {
    $sql = "INSERT INTO mysql_queries ( mysql_queries_table, mysql_queries_field,
                                            mysql_queries_data , mysql_queries_sort   
            VALUES ( '{$table}','{$field}','{$data}','{$sort}' )";
  }
  else {
    $sql  = "UPDATE `mysql_queries` 
             SET mysql_queries_field = '{$field}', mysql_queries_table = '{$table}',
                 mysql_queries_data = '{$data}' , mysql_queries_sort = '{$sort}'
             WHERE mysql_queries_id = '{$id}'";
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
