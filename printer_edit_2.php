<?php
$page = "other";
include "include_config.php";

// Process the form
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

$sql  = "update printer set printer_caption = '" . $_POST['name'];
$sql .= "', printer_location='" . $_POST['location'];
$sql .= "', printer_manufacturer = '" . $_POST['manufacturer'];
$sql .= "', printer_model='" . $_POST['model'];
$sql .= "', printer_serial = '" . $_POST['serial'];
$sql .= "', printer_description = '" . $_POST['description'];
$sql .= "', printer_date_purchased='" . $_POST['date_purchased'];
$sql .= "', printer_value='" . $_POST['value'];
$sql .= "' WHERE printer_id='" . $_GET['printer'] . "'";

$result = mysql_query($sql) or die("Query Failed.<br />$sql");



header("Location: printer_summary.php?printer=" . $_GET["printer"]);



?>





