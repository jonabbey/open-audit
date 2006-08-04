<?php 
$sql = "";
$page = "other";
include "include.php"; 
$bgcolor = $bg1;
echo "<td valign=\"top\">\n";
$SQL = "SELECT * FROM printer WHERE printer_id = '" . $_GET['printer'] . "'";
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    echo "<div class=\"main_each\">";
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">";
    echo "<tr><td colspan=\"2\" class=\"contenthead\">$l_prn $l_sum<br />&nbsp;</td></tr>\n";
    echo "<tr><td colspan=\"2\" class=\"contenthead\"><img src=\"images/printer_l.png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;</td></tr>";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_nam:&nbsp;</td><td>" . $myrow["printer_caption"] . "</td></tr>";
    echo "<tr><td>$l_loc:&nbsp;</td><td>" . $myrow["printer_location"] . "</td></tr>";
    if ($myrow["printer_ip"]) {
      echo "<tr bgcolor=\"$bgcolor\"><td>$l_ipa / $l_mac:&nbsp;</td><td>" . ip_trans($myrow["printer_ip"]) . "&nbsp;&nbsp;-&nbsp;&nbsp;" . return_unknown($myrow["printer_mac_address"]) . "</td></tr>\n";
    } else {
      echo "<tr bgcolor=\"$bgcolor\"><td>$l_att:&nbsp;</td><td>" . $myrow["printer_system_name"] . "</td></tr>\n";
    }
    echo "<tr><td>$l_dav:&nbsp;</td><td>" . return_date($myrow["printer_first_timestamp"]) . "</td></tr>\n";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_mam:&nbsp;</td><td>" . $myrow["printer_manufacturer"] . "</td></tr>\n";
    echo "<tr><td>$l_mdl:&nbsp;</td><td>" . $myrow["printer_model"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_srl:&nbsp;</td><td>" . $myrow["printer_serial"] . "</td></tr>\n";
    echo "<tr><td>$l_dap:&nbsp;(yyyy-mm-dd)&nbsp;</td><td>" . $myrow["printer_date_purchased"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bgcolor\"><td>$l_dol:&nbsp;</td><td>$" . $myrow["printer_value"] . "</td></tr>\n";
    echo "<tr><td valign=\"top\">$l_des:&nbsp;</td><td>" . $myrow["printer_description"] . "</td></tr>\n";
    echo "<tr><td><form action=\"printer_edit.php?printer=" . $_GET['printer'] . "\" method=\"post\"><input name=\"Submit\" value=\" $l_edi \" type=\"submit\" class=\"content\" /></form></td></tr>\n";
    $sql2 = "SELECT * from nmap_ports WHERE nmap_other_id = '" . $myrow["printer_mac_address"] . "' ORDER BY nmap_port_number";
    $result2 = mysql_query($sql2, $db);
    if ($myrow2 = mysql_fetch_array($result2)){
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/nmap_l.png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;Nmap detected open ports</td></tr>\n";
      echo "<tr><td>$l_por</td><td>$l_por $l_name</td></tr>\n";
      do {
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td>" . $myrow2["nmap_port_number"] . "</td><td>" . $myrow2["nmap_port_name"] . "</td></tr>";
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      } while ($myrow2 = mysql_fetch_array($result2));
    } else {
      echo "<tr><td><br />No open ports detected by Nmap.</td></tr>";
    }
  } while ($myrow = mysql_fetch_array($result));
  echo "</table>\n";
} else {}
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
