<?php 
$page = "su";
include "include.php"; 
echo "<td>\n";
$sql = "SELECT MIN(system_timestamp) FROM system WHERE system_uuid = '" . $pc . "'";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
      $first_audited = $myrow["MIN(system_timestamp)"];
  } while ($myrow = mysql_fetch_array($result));
} else {}

$sql = "SELECT * FROM network_card WHERE net_uuid = '" . $pc . "'";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  do {
    if ($myrow["net_ip_address"] <> "000.000.000.000") {
      $subnet = $myrow["net_ip_subnet"];
      $dhcp_enabled = $myrow["net_dhcp_enabled"];
      $dhcp_server = $myrow["net_dhcp_server"];
      $ip = $myrow["net_ip_address"];
    } else {}
  } while ($myrow = mysql_fetch_array($result));
} else {}

    $sql = "SELECT * from system WHERE system_uuid = '$pc' AND system_timestamp = '$timestamp'";
    $result = mysql_query($sql, $db);
    if ($myrow = mysql_fetch_array($result)){
      echo "<div class=\"main_each\">\n";
      echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
      echo "<tr><td class=\"contenthead\" colspan=\"4\">$l_syw $l_sum " . ip_trans($myrow["net_ip_address"]) . " - " . $myrow["system_name"] . "<br />&nbsp;</td></tr>\n";
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br /><img src=\"images/summary_l.png\" width=\"48\" height=\"48\" alt=\"\" />$l_sum</td></tr>\n";
      do {
        echo "<tr bgcolor=\"#F1F1F1\"><td width=\"200\">$l_sys:&nbsp;</td><td>" . $myrow["system_name"] . "</td></tr>\n";
        echo "<tr><td>$l_des:&nbsp;</td><td>" . $myrow["system_description"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_don:&nbsp;</td><td>" . $myrow["net_domain_role"] . "</td></tr>\n";
        echo "<tr><td>$l_reg:&nbsp;</td><td>" . $myrow["system_registered_user"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_cus:&nbsp;</td><td>" . $myrow["net_user_name"] . "</td></tr>\n";
        echo "<tr><td>$l_dom:&nbsp;</td><td>" . $myrow["net_domain"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_cha:&nbsp;</td><td>" . $myrow["system_system_type"] . "</td></tr>\n";
        echo "<tr><td>$l_mdl / $l_srl #:&nbsp;</td><td>" . $myrow["system_model"] . " / " . $myrow["system_id_number"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_mam:&nbsp;</td><td>" . $myrow["system_vendor"] . "</td></tr>\n";
        echo "<tr><td>$l_osy:&nbsp;</td><td>" . $myrow["system_os_name"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_bul / $l_sep:&nbsp;</td><td>" . $myrow["system_build_number"] . " / " . $myrow["system_service_pack"] . "</td></tr>\n";
        echo "<tr><td>$l_syx:&nbsp;</td><td>" . $myrow["system_uuid"] . "&nbsp;&nbsp;</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_osz:&nbsp;</td><td>" . $myrow["date_system_install"] . "</td></tr>\n";
        echo "<tr><td>$l_ipa / $l_sub:&nbsp;</td><td>" . ip_trans($ip) . " / " . ip_trans($subnet) . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_dhe / $l_dhs:&nbsp;</td><td>" . $dhcp_enabled . " / " . $dhcp_server . "</td></tr>\n";
        echo "<tr><td>$l_dav:&nbsp;</td><td>" . return_date_time($myrow["system_first_timestamp"]) . "&nbsp;&nbsp;</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_daw:&nbsp;</td><td>" . return_date_time($timestamp) . "&nbsp;&nbsp;</td></tr>\n";
        echo "<tr><td>$l_syw $l_mem:&nbsp;</td><td>" . $myrow["system_memory"] . "&nbsp;MB</td></tr>\n";
      } while ($myrow = mysql_fetch_array($result));
      } else {}

    $bgcolor = "#FFFFFF";
    $sql = "SELECT * FROM hard_drive WHERE hard_drive_uuid = '$pc' AND hard_drive_timestamp = '$timestamp' ORDER BY hard_drive_index";
    $result = mysql_query($sql, $db);
    if ($myrow = mysql_fetch_array($result)){
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_hde $l_siz:&nbsp;</td><td>" . number_format($myrow["hard_drive_size"]) . " MB</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    } else {}


    $sql = "SELECT * FROM processor WHERE processor_uuid = '$pc' AND processor_timestamp = '$timestamp' ORDER BY processor_id";
    $result = mysql_query($sql, $db);
    if ($myrow = mysql_fetch_array($result)){
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_pro:&nbsp;</td><td>" . $myrow["processor_name"] . "</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    } else {}



    $sql = "SELECT * FROM system_man WHERE system_man_uuid = '$pc'";
    $result = mysql_query($sql, $db);
    if ($myrow = mysql_fetch_array($result)){
    do {
    $s_m_l = $myrow['system_man_location'];
    $s_m_d_o_p = $myrow['system_man_date_of_purchase'];
    $s_m_v = $myrow['system_man_value'];
    $s_m_s_n = $myrow['system_man_serial_number'];
    $s_m_d = ereg_replace( "\n", "<br />", $myrow['system_man_description'] );
    } while ($myrow = mysql_fetch_array($result));
    } else {
    $s_m_l = "";
    $s_m_d_o_p = "";
    $s_m_v = "";
    $s_m_s_n = "";
    $s_m_d = "";
    }
echo "<form action=\"system_summary_edit_2.php?sub=no&other=" . $pc . "\" method=\"POST\">\n";
echo "<tr bgcolor=\"#F1F1F1\"><td>Location:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"location\" size=\"20\" value=\"" . $s_m_l . "\" class=\"content\"></td></tr>\n";
echo "<tr><td>Date of Purchase:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"date\" size=\"20\" value=\"" . $s_m_d_o_p . "\" class=\"content\">(yyyy-mm-dd)</td></tr>\n";
echo "<tr bgcolor=\"#F1F1F1\"><td>Dollar Value:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"dollar\" size=\"20\" value=\"" . $s_m_v . "\" class=\"content\"></td></tr>\n";
echo "<tr><td>Asset Tag:&nbsp;</td><td><input type=\"text\" class=\"for_forms\" name=\"serial\" size=\"20\" value=\"" . $s_m_s_n . "\" class=\"content\"></td></tr>\n";
echo "<tr bgcolor=\"#F1F1F1\"><td>Description:</td><td><input type=\"text\" class=\"for_forms\" name=\"description\" size=\"20\" value=\"" . $s_m_d . "\" class=\"content\"></td></tr>\n";
echo "<tr><td><input name=\"Submit\" value=\"$l_sut\" type=\"Submit\" class=\"content\"></td></tr>\n";
echo "<input type=\"hidden\" value=\"$pc\" name=\"pc\">\n";
echo "</form>\n";echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
