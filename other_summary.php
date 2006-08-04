<?php
$sql = "";
$page = "other";
include "include.php"; 
echo "<td>\n";
$sql = "SELECT * FROM other WHERE other_id = '" . $_GET['other'] . "'";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
do {
  echo "<div class=\"main_each\">";
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
  echo "<tr><td class=\"contenthead\" colspan=\"2\">$l_sum " . ip_trans($myrow["other_ip_address"]) . " - " . ucwords($myrow["other_type"]) . "<br />&nbsp;</td></tr>\n";
  echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/o_" . url_clean($myrow["other_type"]) . ".png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;" . $myrow["other_network_name"] . "</td></tr>";
  echo "<tr bgcolor=\"$bg1\"><td>$l_nam:&nbsp;</td><td>" . $myrow["other_network_name"] . "</td></tr>";
  echo "<tr><td>$l_typ:&nbsp;</td><td>" . $myrow["other_type"] . "</td></tr>";
  if ($myrow["other_linked_pc"] <> ""){
    $SQL2 = "select * from system WHERE system_uuid = '" . $myrow["other_linked_pc"] . "'";
    $result2 = mysql_query($SQL2, $db);
    if ($myrow2 = mysql_fetch_array($result2)){
      do {
          echo "<tr bgcolor=\"$bg1\"><td>$l_att:&nbsp;</td><td>" . $myrow2["net_ip_address"] . "  -  " . $myrow2["system_name"] . "</td></tr>\n";
      } while ($myrow2 = mysql_fetch_array($result2));
    } else {}
  } else {
    echo "<tr bgcolor=\"$bg1\"><td>$l_att:&nbsp;</td><td>" . $myrow["other_linked_pc"] . "</td></tr>\n";
  }
    echo "<tr><td>$l_ipa:&nbsp;</td><td>" . ip_trans($myrow["other_ip_address"]) . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_mac:&nbsp;</td><td>" . $myrow["other_mac_address"] . "</td></tr>\n";
    echo "<tr><td>$l_dav:&nbsp;</td><td>" . return_date_time($myrow["other_timestamp"]) . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_mam:&nbsp;</td><td>" . $myrow["other_manufacturer"] . "</td></tr>\n";
    echo "<tr><td>$l_mdl:&nbsp;</td><td>" . $myrow["other_model"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_srl:&nbsp;</td><td>" . $myrow["other_serial"] . "</td></tr>\n";
    echo "<tr><td>$l_loc:&nbsp;</td><td>" . $myrow["other_location"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_dap:&nbsp;(yyyy-mm-dd)&nbsp;</td><td>" . $myrow["other_date_purchased"] . "</td></tr>\n";
    echo "<tr><td>$l_dol:&nbsp;</td><td>$" . $myrow["other_value"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td valign=\"top\">$l_des:&nbsp;</td><td>" . $myrow["other_description"] . "</td></tr>\n";
    echo "<tr><td><form action=\"other_edit.php?other=" . $_GET['other'] . "\" method=\"post\"><input name=\"Submit\" value=\" Edit \" type=\"submit\" class=\"content\" /></form></td></tr>\n";
    $SQL2 = "SELECT * from nmap_ports WHERE nmap_other_id = '" . $myrow["other_id"] . "' ORDER BY nmap_port_number";
    $result2 = mysql_query($SQL2, $db);
    echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/nmap_l.png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;$l_nmb</td></tr>\n";
    echo "<tr><td>$l_por $l_num</td><td>$l_por $l_nam</td></tr>\n";
    if ($myrow2 = mysql_fetch_array($result2)){
      do {
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td>" . $myrow2["nmap_port_number"] . "</td><td>" . $myrow2["nmap_port_name"] . "</td></tr>\n";
      } while ($myrow2 = mysql_fetch_array($result2));
    } else {
    echo "<tr><td>No open ports detected by Nmap.</td></tr>";
    }
  } while ($myrow = mysql_fetch_array($result));
} else {}
echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
