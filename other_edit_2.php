<?php
$page = "other";
include "include_config.php";

// Process the form
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

if (isset($_POST["name"])){$name = $_POST["name"];}else{$name="";}

$sql  = "update other set other_network_name = '$name',";
$sql .= " other_ip_address = '" . $_POST['ip'] . "',";
$sql .= " other_mac_address = '" . $_POST['mac_address'] . "',";
$sql .= " other_description = '" . $_POST['description'] . "',";
$sql .= " other_serial = '" . $_POST['serial'] . "',";
$sql .= " other_manufacturer = '" . $_POST['manufacturer'] . "',";
$sql .= " other_model='" . $_POST['model'] . "',";
$sql .= " other_type='" . $_POST['type'] . "',";
$sql .= " other_location='" . $_POST['location'] . "',";
$sql .= " other_date_purchased='" . $_POST['date'] . "',";
$sql .= " other_value='" . $_POST['dollar'] . "',";
$sql .= " other_linked_pc='" . $_POST['linked_pc'] . "' ";
$sql .= " WHERE other_id='" . $_GET['other'] . "'";

$result = mysql_query($sql);

if ($_GET["page"] == "printer"){
  header("Location: printer_summary.php?printer=" . $_GET["other"]);
} else {
  header("Location: other_summary.php?other=" . $_GET["other"]);
}


?>





