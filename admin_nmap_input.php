<?php
$page = "add_pc";
include "include.php";
echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";

echo "<p class=\"contenthead\">".__("NMap")."</p>\n";
//
// Avoid undeclared vars warnings (AJH).
//
$device_type="unknown";
$running="unknown";
$ip_address="000.000.000.000";
$manufacturer="unknown";
$mac="00:00:00:00:00:00";
//
$timestamp = date("YmdHis");
$uuid = "";
$process = "";
if (isset($_POST["submit"])){
  $input = $_POST['add'];
  $input = explode("\n", $input);

  foreach ($input as $split) {
    if (substr($split, 0, 12) == "MAC Address:") {
      // OK - we have a hit.
      $mac = substr($split,13,17);
      echo "Mac Address: " . $mac . "<br />";
      $temp = explode(")",substr($split, strpos($split, "(")+1));
      $manufacturer = $temp[0];
      echo "Manufacturer: " . $manufacturer . "<br />";
    }
    if (substr($split, 0, 12) == "Device type:") {
      // OK - we have a hit.
      $temp = explode(":", $split);
      $temp2 = explode("|",$temp[1]);
      $device_type = ltrim(rtrim($temp2[0]));
      echo "Device Type: " . $device_type . "<br />";
    }
    if (substr($split, 0, 8) == "Running:") {
      // OK - we have a hit.
      $temp = explode(":", $split);
      $running = ltrim(rtrim($temp[1]));
      echo "Running: " . $running . "<br />";
    }
    if (substr($split, 0, 20) == "Interesting ports on") {
      // OK - we have a hit.
      if (strpos($split, ")") !== false){
        $temp = explode(")",substr($split, strpos($split, "(")+1));
        $ip_address = $temp[0];
        echo "IP Address: " . $ip_address . "<br />";
        $temp = explode(" ", $split);
        $temp2 = explode(".", $temp[3]);
        $name = $temp2[0];
        echo "Name: " . $name . "<br />";
      } else {
        $temp = explode(" ",$split);
        $temp2 = $temp[3];
        $temp = explode(":",$temp2);
        $ip_address = $temp[0];
        $ip_explode = explode(".",$ip_address);
        if (strlen($ip_explode[0]) < 2){$ip_explode[0] = "0" . $ip_explode[0];}
        if (strlen($ip_explode[0]) < 3){$ip_explode[0] = "0" . $ip_explode[0];}
        if (strlen($ip_explode[1]) < 2){$ip_explode[1] = "0" . $ip_explode[1];}
        if (strlen($ip_explode[1]) < 3){$ip_explode[1] = "0" . $ip_explode[1];}
        if (strlen($ip_explode[2]) < 2){$ip_explode[2] = "0" . $ip_explode[2];}
        if (strlen($ip_explode[2]) < 3){$ip_explode[2] = "0" . $ip_explode[2];}
        if (strlen($ip_explode[3]) < 2){$ip_explode[3] = "0" . $ip_explode[3];}
        if (strlen($ip_explode[3]) < 3){$ip_explode[3] = "0" . $ip_explode[3];}
        $ip_address = $ip_explode[0] . "." . $ip_explode[1] . "." . $ip_explode[2] . "." . $ip_explode[3];
        echo "IP Address: " . $ip_address . "<br />";
        $name = $ip_address;
        echo "Name: " . $name . "<br />";
      }
    }
	if (substr($split, 0, 25) == "All 1697 scanned ports on") {
      // OK - we have a hit (all scanned ports are closed or filtered).
      if (strpos($split, ")") !== false){
        $temp = explode(")",substr($split, strpos($split, "(")+1));
        $ip_address = $temp[0];
        echo "IP Address: " . $ip_address . "<br />";
        $temp = explode(" ", $split);
        $temp2 = explode(".", $temp[5]);
        $name = $temp2[0];
        echo "Name: " . $name . "<br />";
      } else {
        $temp = explode(" ",$split);
        $ip_address = $temp[5];
        $ip_explode = explode(".",$ip_address);
        if (strlen($ip_explode[0]) < 2){$ip_explode[0] = "0" . $ip_explode[0];}
        if (strlen($ip_explode[0]) < 3){$ip_explode[0] = "0" . $ip_explode[0];}
        if (strlen($ip_explode[1]) < 2){$ip_explode[1] = "0" . $ip_explode[1];}
        if (strlen($ip_explode[1]) < 3){$ip_explode[1] = "0" . $ip_explode[1];}
        if (strlen($ip_explode[2]) < 2){$ip_explode[2] = "0" . $ip_explode[2];}
        if (strlen($ip_explode[2]) < 3){$ip_explode[2] = "0" . $ip_explode[2];}
        if (strlen($ip_explode[3]) < 2){$ip_explode[3] = "0" . $ip_explode[3];}
        if (strlen($ip_explode[3]) < 3){$ip_explode[3] = "0" . $ip_explode[3];}
        $ip_address = $ip_explode[0] . "." . $ip_explode[1] . "." . $ip_explode[2] . "." . $ip_explode[3];
        echo "IP Address: " . $ip_address . "<br />";
        $name = $ip_address;
        echo "Name: " . $name . "<br />";
      }
    }
  } // End of for each
  if ($device_type == ""){$device_type = "unknown";}
  if ($running == ""){$running = "unknown";}
  if (substr_count($device_type, "general purpose") > "0"){
    if (substr_count($running, "Linux") > "0")   { $device_type = "os_linux";}
    if (substr_count($running, "Windows") > "0") { $device_type = "os_windows"; echo "Windows.<br />";}
    if (substr_count($running, "unix") > "0")    { $device_type = "os_unix";}
    if (substr_count($running, "MAC") > "0")     { $device_type = "os_mac";}
    if (substr_count($running, "AIX") > "0")     { $device_type = "os_unix";}
    if (substr_count($running, "SCO UnixWare") > "0"){ $device_type = "os_unix";}
  } else {}

    if (isset($mac) AND $mac <> "00:00:00:00:00:00"){
    // First check the network_card table
    $sql = "SELECT net_uuid FROM network_card WHERE net_mac_address = '" . $mac . "'";
    echo $sql . "<br />";
    $result = mysql_query($sql) or die ('Query Failed: <br />$sql<br />' . mysql_error() . '<br />' . $sql);
    $myrow = mysql_fetch_array($result);
    if (isset($myrow["net_uuid"])){
      $process = "network_mac";
      $uuid = $myrow["net_uuid"];
    } else {
      // Not in network_card - check other table
      $sql = "SELECT other_id, other_mac_address FROM other WHERE other_mac_address = '" . $mac . "' OR other_ip_address = '" . ip_trans_to($ip_address) . "' ORDER BY other_timestamp";
      echo $sql . "<br />";
      $result = mysql_query($sql) or die ('Query Failed: <br />$sql<br />' . mysql_error() . '<br />' . $sql);
      $myrow = mysql_fetch_array($result);
      if (isset($myrow["other_id"])){
        $process = "other_mac";
        $uuid = $myrow["other_id"];
        if ($myrow["other_mac_address"] <> ""){$mac = $myrow["other_mac_address"];}
      }
    }
  } else {}

  if ($mac == "00:00:00:00:00:00"){
    $sql = "SELECT net_uuid FROM network_card WHERE net_ip_address = '" . $ip_address . "'";
    echo $sql . "<br />";
    $result = mysql_query($sql) or die ('Query Failed: <br />$sql<br />' . mysql_error() . '<br />' . $sql);
    $myrow = mysql_fetch_array($result);
    if (isset($myrow["net_uuid"])){
      $process = "network_ip";
      $uuid = $myrow["net_uuid"];
    } else {
      $sql = "SELECT other_id FROM other WHERE other_ip_address = '" . ip_trans_to($ip_address) . "'";
      echo $sql . "<br />";
      $result = mysql_query($sql) or die ('Query Failed: <br />$sql<br />' . mysql_error() . '<br />' . $sql);
      $myrow = mysql_fetch_array($result);
      if (isset($myrow["other_id"])){
        $process = "other_ip";
        $uuid = $myrow["other_id"];
      } else {}
    }
  } else {}


  if ($uuid == "" and $mac <> "00:00:00:00:00:00") {
    // Insert into other table
    $sql  = "INSERT INTO other (other_network_name, other_ip_address, other_mac_address, ";
    $sql .= "other_description, other_manufacturer, other_type, ";
    $sql .= "other_timestamp, other_first_timestamp) VALUES (";
    $sql .= "'$name','" . ip_trans_to($ip_address) . "','$mac',";
    $sql .= "'$running','$manufacturer','$device_type',";
    $sql .= "'$timestamp','$timestamp')";
    $result = mysql_query($sql) or die ('Insert Failed: <br />' . $sql . '<br />' . mysql_error());
    $uuid = mysql_insert_id();
    $process = "new_other";
    echo $sql . "<br />";
  } else {}

  if ($process == "other_mac"){
    $sql  = "UPDATE other SET other_ip_address = '". ip_trans_to($ip_address) . "', ";
    $sql .= "other_mac_address = '$mac', other_timestamp = '$timestamp' ";
    $sql .= "WHERE other_id = '$uuid'";
    $result = mysql_query($sql) or die ('Insert Failed: <br />' . $sql . '<br />' . mysql_error());
    //$uuid = mysql_insert_id();
    $process = "update_other";
    echo $sql . "<br />\n";
  } else {}

  if ($process <> ""){
    // Process the file
    echo "UUID: " . $uuid . "<br />";
    echo "Process: " . $process . "<br />";
    $sql = "DELETE FROM nmap_ports WHERE nmap_other_id = '" . $uuid . "'";
    echo $sql . "<br />\n";
    $result = mysql_query($sql) or die ('Delete Failed: <br />' . $sql . '<br />' . mysql_error());
    foreach ($input as $split) {
      if ((strpos($split, "open") === false) OR (strpos($split, "/tcp") === false)){
      } else {
        $temp = explode("/", $split);
        $port_number = $temp[0];
        $pos = intval(strpos($split, "open")) + 6;
        $port_name = rtrim(substr($split, $pos));
        $sql  = "INSERT INTO nmap_ports (nmap_other_id, nmap_port_number, nmap_port_name, nmap_timestamp) VALUES (";
        $sql .= "'" . $uuid . "','" . $port_number . "','" . $port_name . "','" . $timestamp . "')";
        $result = mysql_query($sql) or die ('Insert Failed: <br />' . $sql . '<br />' . mysql_error());
        echo $sql . "<br />";
      }
    } // End of for each
  } // End of isset($mac)




echo "<br />" .$sql . "<br />";





} else {

  echo "<form action=\"admin_nmap_input.php\" method=\"post\">\n";
  echo "<table>\n";
  echo "<tr><td colspan=\"2\"><textarea rows=\"20\" name=\"add\" cols=\"90\" class=\"for_forms\"></textarea></td></tr>\n";
  echo "<tr><td colspan=\"2\"><input name=\"submit\" value=\"".__("Save")."\" type=\"submit\" /></td></tr>\n";
  echo "</table>\n";
  echo "</form>\n";

}

echo "</div>\n";
echo "</div>\n";
echo "</td>\n";
echo "</body>\n";
echo "</html>\n";
?>
