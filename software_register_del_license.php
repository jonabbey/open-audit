<?php
	
include "include_config.php";

	if ($_GET['confirm']=1) {

	$link = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
	mysql_select_db("$mysql_database") or die("Could not select database");

	$query = "DELETE FROM software_licenses WHERE license_id = '" . $_GET['id'] . "'";
	$result = mysql_query($query)  or die("Query failed at insert stage. license");

	header("Location: software_register_details.php?id=" . $_GET["id2"]);
	} else {}

?>