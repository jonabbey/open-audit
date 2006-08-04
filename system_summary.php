<?php 
$page = "su";
include "include.php"; 
echo "<td>\n";
$subnet = "";
$dhcp_enabled = "";
$dhcp_server = "";
$ip = "";

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

    $sql = "SELECT * from system WHERE system_uuid = '$pc'";
    $result = mysql_query($sql, $db);
    if ($myrow = mysql_fetch_array($result)){
      $timestamp = $myrow["system_timestamp"];
      $img = "";
      $os_name = $myrow["system_os_name"];
      if (substr_count($os_name, "Ubuntu") > 0)    {$img = "<img src=\"images/linux_ubuntu_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Ubuntu\" />";}
      if (substr_count($os_name, "Red Hat") > 0)   {$img = "<img src=\"images/linux_redhat_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Red Hat\" />";}
      if ((substr_count($os_name, "Mandrake") > 0) OR (substr_count($os_name, "Mandriva") > 0)) {$img = "<img src=\"images/linux_mandriva_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Mandrake\" />";}
      if (substr_count($os_name, "Fedora") > 0)    {$img = "<img src=\"images/linux_fedora_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Fedora\" />";}
      if (substr_count($os_name, "Debian") > 0)    {$img = "<img src=\"images/linux_debian_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Debian\" />";}
      if (substr_count($os_name, "Slackware") > 0) {$img = "<img src=\"images/linux_slackware_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Slackware\" />";}
      if ((substr_count($os_name, "Suse") > 0) OR (substr_count($os_name, "Novell") > 0)){$img = "<img src=\"images/linus_suse_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Suse\" />";}
      if (substr_count($os_name, "Gentoo") > 0)    {$img = "<img src=\"images/linux_gentoo_l.png\" width=\"48\" height=\"48\" alt=\"$l_m58\" title=\"Gentoo\" />";}
      if ($img == ""){$img = "<a href= \"launch_rdp.php?launch=".$myrow["system_name"].".rdp\"/><img src=\"images/summary_l.png\" width=\"48\" height=\"48\" alt=\"\" title=\"Windows\" />";}

      echo "<div class=\"main_each\">\n";
      echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
      echo "<tr><td class=\"contenthead\" colspan=\"4\">$l_syw $l_sum " . ip_trans($myrow["net_ip_address"]) . " - " . $myrow["system_name"] . "<br />&nbsp;</td></tr>\n";
      // 
      echo "<tr><td class=\"contenthead\" colspan=\"2\"><br />" . $img . "$l_sum</td></tr>\n";
      do {
      // Click the name to connect using RDP
        echo "<tr bgcolor=\"#F1F1F1\"><td width=\"200\">$l_sys:&nbsp;</td><td>" . $myrow["system_name"] . "</td></tr>\n";
        echo "<tr><td>$l_des:&nbsp;</td><td>" . $myrow["system_description"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_don:&nbsp;</td><td>" . $myrow["net_domain_role"] . "</td></tr>\n";
        echo "<tr><td>$l_reg:&nbsp;</td><td>" . $myrow["system_registered_user"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_cus:&nbsp;</td><td>" . $myrow["net_user_name"] . "</td></tr>\n";
        echo "<tr><td>$l_dom:&nbsp;</td><td>" . $myrow["net_domain"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_cha:&nbsp;</td><td>" . $myrow["system_system_type"] . "</td></tr>\n";
        echo "<tr><td>$l_mdl / $l_srl #:&nbsp;</td><td>" . $myrow["system_model"] . " / " . $myrow["system_id_number"] . "</td></tr>\n";
        echo "<tr bgcolor=\"#F1F1F1\"><td>$l_mam:&nbsp;</td><td>" . $myrow["system_vendor"];
        if ($myrow["system_vendor"] == "Dell Inc." || $myrow["system_vendor"] == "Dell Computer Corporation") {
          echo " / <a href='http://support.dell.com/support/topics/global.aspx/support/my_systems_info/en/details?c=us&amp;cs=usbsdt1&amp;servicetag=" . $myrow["system_id_number"] . "' target=_blank>Warranty Information</a> / <a href='http://support.dell.com/support/downloads/index.aspx?c=us&amp;l=en&amp;s=gen&amp;servicetag=" . $myrow["system_id_number"] . "' target=_blank>Drivers &amp; Software</a>"; 
        } elseif ($myrow["system_vendor"] == "Compaq") { 
          echo " / <a href='http://www4.itrc.hp.com/service/ewarranty/warrantyResults.do?BODServiceID=NA&&amp;RegisteredPurchaseDate=&&amp;country=GB&&amp;productNumber=&&amp;serialNumber1=" . $myrow["system_id_number"] . "' target=_blank>Warranty Information</a> / <a href='http://h20180.www2.hp.com/apps/Lookup?h_lang=en&h_cc=uk&cc=uk&h_page=hpcom&lang=en&h_client=S-A-R135-1&h_pagetype=s-002&h_query=" . $myrow["system_id_number"] . "' target=_blank>Drivers & Software</a>"; 
        } elseif ($myrow["system_vendor"] == "IBM") { 
          echo " / <a href='http://www-307.ibm.com/pc/support/site.wss/quickPath.do?quickPathEntry=" . $myrow["system_model"] . "' target=_blank>Product Page</a>";
        } elseif ($myrow["system_vendor"] == "Gateway") {
          echo " / <a href='http://support.gateway.com/support/allsysteminfo.asp?sn=" . $myrow["system_id_number"] . "' target=_blank>Support Page</a>";
        } else {}
        echo "</td></tr>\n";
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

    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_loc:&nbsp;</td><td>" . $s_m_l . "</td></tr>\n";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_dap:&nbsp;</td><td>" . $s_m_d_o_p . "</td></tr>\n";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_dol:&nbsp;</td><td>" . $s_m_v . "</td></tr>\n";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_ast:&nbsp;</td><td>" . $s_m_s_n . "</td></tr>\n";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_des:&nbsp;</td><td>" . $s_m_d . "</td></tr>\n";

    $sql = "SELECT * FROM other WHERE other_linked_pc = '" . $pc . "'";
    $result = mysql_query($sql, $db);
    if ($myrow = mysql_fetch_array($result)){
      do {
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">Other Items:&nbsp;</td><td>" . $myrow["other_type"] . "&nbsp;&nbsp;:&nbsp;&nbsp;<a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;:&nbsp;&nbsp;" . $myrow["other_manufacturer"] . "</td></tr>";
      } while ($myrow = mysql_fetch_array($result));
    } else {
    //  $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    //  echo "<tr><td>$l_oth:&nbsp;</td><td>None</td></tr>\n";
    }

    $sql = "SELECT * FROM monitor WHERE monitor_uuid = '" . $pc . "'";
    $result = mysql_query($sql, $db);
    if ($myrow = mysql_fetch_array($result)){
      do {
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">$l_mon:&nbsp;</td><td><a href=\"monitor_summary.php?monitor=" . $myrow["monitor_id"] . "&amp;sub=1\">" . $myrow["monitor_manufacturer"] . "&nbsp;&nbsp;:&nbsp;&nbsp;" . $myrow["monitor_model"] . "</a></td></tr>";
      } while ($myrow = mysql_fetch_array($result));
    } else {
    //  $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    //  echo "<tr><td>$l_mon:&nbsp;</td><td>None</td></tr>\n";
    }
echo "<tr><td><form action=\"system_summary_edit.php?pc=" .  $pc . "&amp;sub=all\" method=\"post\"><input name=\"submit\" value=\" $l_edi \" type=\"submit\" /></form></td></tr>";
echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
