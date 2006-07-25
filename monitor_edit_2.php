<?php
$page = "other";
include "include_config.php";

// Process the form
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

$sql  = "update monitor set monitor_date_purchased = '" . $_POST['date_purchased'];
$sql .= "', monitor_purchase_order_number='" . $_POST['po_number'];
$sql .= "', monitor_value = '" . $_POST['value'];
$sql .= "', monitor_description = '" . $_POST['description'];
$sql .= "' WHERE monitor_id='" . $_GET['monitor'] . "'";

$result = mysql_query($sql) or die("Query Failed.<br />$sql");



header("Location: monitor_summary.php?monitor=" . $_GET["monitor"]);



?>





