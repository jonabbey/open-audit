<?php 
$sql = "";
$page = "other";
include "include.php"; 
$bgcolor = $bg1;
echo "<td valign=\"top\">\n";
$sql = "SELECT * FROM printer WHERE printer_id = '" . $_GET['printer'] . "'";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<div class=\"main_each\">\n";
  echo "  <form action=\"printer_edit_2.php?sub=no&amp;printer=" . $_GET['printer'] . "\" method=\"post\">\n";
  echo "    <table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
  echo "      <tr><td colspan=\"2\" class=\"contenthead\">Printer Edit<br />&nbsp;</td></tr>\n";
  echo "      <tr><td colspan=\"2\" class=\"contenthead\"><img src=\"images/printer_l.png\" width=\"48\" height=\"48\" alt=\"\" /></td></tr>";
  echo "      <tr bgcolor=\"$bgcolor\"><td>$l_nam:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"name\" size=\"30\" value=\"" . $myrow['printer_caption'] . "\" /></td></tr>";
  echo "      <tr><td>$l_loc:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"location\" size=\"30\" value=\"" . $myrow['printer_location'] . "\" /></td></tr>";
  if ($myrow["printer_ip"]) {
    echo "      <tr bgcolor=\"$bgcolor\"><td>$l_ipa / $l_mac:&nbsp;</td><td>" . ip_trans($myrow["printer_ip"]) . "&nbsp;&nbsp;-&nbsp;&nbsp;" . return_unknown($myrow["printer_mac_address"]) . "</td></tr>\n";
  } else {
    echo "      <tr bgcolor=\"$bgcolor\"><td>$l_att:&nbsp;</td><td>" . $myrow["printer_system_name"] . "</td></tr>\n";
  }
  echo "      <tr><td>$l_dav:&nbsp;</td><td>" . return_date($myrow["printer_first_timestamp"]) . "</td></tr>\n";
  echo "      <tr bgcolor=\"$bgcolor\"><td>$l_mam:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"manufacturer\" size=\"30\" value=\"" . $myrow['printer_manufacturer'] . "\" /></td></tr>\n";
  echo "      <tr><td>$l_mdl:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"model\" size=\"30\" value=\"" . $myrow['printer_model'] . "\" /></td></tr>\n";
  echo "      <tr bgcolor=\"$bgcolor\"><td>$l_srl:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"serial\" size=\"30\" value=\"" . $myrow['printer_serial'] . "\" /></td></tr>\n";
  echo "      <tr><td>$l_dap:&nbsp;(yyyy-mm-dd)&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"date_purchased\" size=\"30\" value=\"" . $myrow['printer_date_purchased'] . "\" /></td></tr>\n";
  echo "      <tr bgcolor=\"$bgcolor\"><td>$l_dol:&nbsp;$</td><td><input type=\"text\" class=\"for_forms\" name=\"value\" size=\"30\" value=\"" . $myrow['printer_value'] . "\" /></td></tr>\n";
  echo "      <tr><td valign=\"top\">$l_des:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"description\" size=\"30\" value=\"" . $myrow['printer_description'] . "\" /></td></tr>\n";
  echo "      <tr><td><input name=\"Submit\" value=\"$l_sut\" type=\"Submit\" /></td></tr>\n";
  echo "    </table>\n";
  echo "  </form>\n";
} else {}
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
