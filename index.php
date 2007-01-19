<?php
$page = "";
$extra = "";
$software = "";
$count = 0;
$total_rows = 0;
$latest_version = "06.09.29";

// Check for config, otherwise run setup
@(include_once "include_config.php") OR die(header("Location: setup.php"));

if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
if (isset($_GET['validate'])) {$validate = $_GET['validate'];} else {$validate= "n";}

include "include.php";

$title = "";
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

echo "<td>\n";

// Check to see if there is an update
if (versionCheck(get_config("version"), $latest_version)) {
  echo "<div class=\"main_each\">
          <div style=\"float: right\">
            <img src=\"images/emblem_important.png\" height=\"24\" width=\"24\" alt=\"\" />
          </div>
          <div style=\"float: left\">
            <img src=\"images/emblem_important.png\" height=\"24\" width=\"24\" alt=\"\" />
          </div>
          <div class=\"indexheadlines\" align=\"center\">";
  echo __("An update has been found.");
  echo " <a href=\"upgrade.php\">";
  echo __("Click here to upgrade!");
  echo "</a></div><br /></div>";
}





if ($show_system_discovered == "y") {
  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_first_timestamp FROM system ";
  $sql .= "WHERE system_first_timestamp > '" . adjustdate(0,0,-$system_detected) . "000000' ORDER BY system_name";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
         <tr>
           <td class=\"indexheadlines\" colspan=\"3\"><a href=\"rss_new_systems.php\"><img src=\"images/feed-icon.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a>&nbsp;<a href=\"javascript://\" onclick=\"switchUl('f1');\">".__("Systems Discovered in the last")." $system_detected ".__("Days").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f1');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
          </tr>
       </table>

       <div style=\"display:none;\" id=\"f1\">\n";

  if ($myrow = mysql_fetch_array($result)){

    echo
        "<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
           <tr>
             <td><b>".__("IP Address")."</b></td>
             <td><b>".__("Hostname")."</b></td>
             <td><b>".__("Date Audited")."</b></td>
           </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
           <td>" . return_date_time($myrow["system_first_timestamp"]) . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
  } else {}
  echo
    "</div>
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>".__("Systems").": " . mysql_numrows($result) . "</b></td>
         </tr>
       </table>
    </div>\n";

} else {}







if ($show_other_discovered == "y") {
  $sql  = "SELECT * FROM other WHERE (other_ip_address <> '' AND ";
  $sql .= "other_first_timestamp > '" . adjustdate(0,0,-$other_detected) . "000000') ORDER BY other_ip_address";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";
  echo
    "<div class=\"main_each\">
      <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
        <tr>
          <td class=\"indexheadlines\" colspan=\"3\"><a href=\"rss_new_other.php\"><img src=\"images/feed-icon.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a>&nbsp;<a href=\"javascript://\" onclick=\"switchUl('f0');\">".__("Other Items Discovered in the last")." $other_detected ".__("Days").".</a></td>
          <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f0');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
        </tr>
      </table>

      <div style=\"display:none;\" id=\"f0\">\n";

  if ($myrow = mysql_fetch_array($result)){
    echo
    "<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
       <tr>
         <td style=\"width:150px;\"><b>".__("IP Address")."</b></td>
         <td style=\"width:150px;\"><b>".__("Hostname")."</b></td>
         <td style=\"width:100px;\"><b>".__("Type")."</b></td>
         <td style=\"width:250px;\"><b>".__("Description")."</b></td>
       </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["other_type"] . "&nbsp;</td>
           <td>" . $myrow["other_description"] . "&nbsp;&nbsp;&nbsp;</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
    $total_rows = mysql_num_rows($result);
  } else {}

  echo
    "</div>
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr><td colspan=\"3\"><b>".__("Other Items").": " . $total_rows . "</b></td></tr>
        </table>
    </div>\n";

} else {}






if ($show_systems_not_audited == "y") {
  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_timestamp FROM system WHERE ";
  $sql .= "system_timestamp < '" . adjustdate(0,0,-$days_systems_not_audited) . "000000' ORDER BY system_name";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">
         <tr>
           <td class=\"indexheadlines\" colspan=\"3\"><a href=\"javascript://\" onclick=\"switchUl('f2');\">".__("Systems Not Audited in the last")." $days_systems_not_audited ".__("Days").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f2');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f2\">\n";

  if ($myrow = mysql_fetch_array($result)){

    echo
      "<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
             <td><b>".__("IP Address")."</b></td>
             <td><b>".__("Hostname")."</b></td>
             <td><b>".__("Date Audited")."</b></td>
         </tr>\n";

    do {
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
           <td>" . return_date_time($myrow["system_timestamp"]) . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
  } else {}

  echo
    "</div>
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>".__("Systems").": " . mysql_numrows($result) . "</b></td>
         </tr>
       </table>
     </div>\n";

} else {}









if ($show_partition_usage == "y") {
  $sql  = "SELECT sys.system_name, sys.net_ip_address, par.partition_uuid, par.partition_volume_name, ";
  $sql .= "par.partition_caption, par.partition_free_space, par.partition_size, par.partition_timestamp ";
  $sql .= "FROM system sys, partition par WHERE par.partition_free_space < '$partition_free_space' ";
  $sql .= "AND sys.system_uuid = par.partition_uuid AND par.partition_timestamp = sys.system_timestamp ";
  $sql .= "ORDER BY sys.system_name, par.partition_caption";
  $result = mysql_query($sql, $db);
    $bgcolor = "#FFFFFF";
    echo
      "<div class=\"main_each\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td class=\"indexheadlines\"><a href=\"javascript://\" onclick=\"switchUl('f3');\">".__("Partition free space less than")." $partition_free_space ".__("MB").".</a></td>
             <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f3');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
           </tr>
         </table>
       <div style=\"display:none;\" id=\"f3\">";

    if ($myrow = mysql_fetch_array($result)){

      echo
        "<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td style=\"width:150px;\"><b>".__("IP Address")."</b></td>
             <td style=\"width:150px;\"><b>".__("Hostname")."</b></td>
             <td style=\"width:150px;\"><b>".__("Free Space")." ".__("MB")."</b></td>
             <td style=\"width:150px;\"><b>".__("Size")."</b></td>
             <td style=\"width:150px;\"><b>".__("Free Space")." %</b></td>
             <td style=\"width:150px;\"><b>".__("Drive Letter")."</b></td>
             <td style=\"width:150px;\"><b>".__("Volume Name")."</b></td>
           </tr>\n";

      do {
        if ($myrow["partition_size"] <> 0){
          $percent_free = round((($myrow["partition_free_space"] / $myrow["partition_size"]) * 100),1);
        } else {
          $percent_free = 0;
        }
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
             <td><a href=\"system.php?pc=" . $myrow["partition_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
             <td>" . $myrow["partition_free_space"] . " Mb</td>
             <td>" . $myrow["partition_size"] . " Mb</td>
             <td>" . $percent_free . " %</td>
             <td>" . $myrow["partition_caption"] . " </td>
             <td>" . $myrow["partition_volume_name"] . " </td>
           </tr>\n";

      } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
  } else {}

  echo
    "</div>
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>".__("Partitions").": " . mysql_numrows($result) . "</b></td>
         </tr>
       </table>
     </div>\n";

} else {}

if ($show_software_detected == "y"){
  $sql  = "SELECT sw.software_name, sw.software_first_timestamp, sys.system_name, sys.system_uuid, ";
  $sql .= "sys.net_ip_address FROM software sw, system sys WHERE ";
  $sql .= "software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
  $sql .= "AND sys.system_first_timestamp < '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
  $sql .= "AND sw.software_name NOT LIKE '%Hotfix%' AND sw.software_name NOT LIKE '%Update%' AND ";
  $sql .= "sw.software_timestamp = sys.system_timestamp AND ";
  $sql .= "sw.software_uuid = sys.system_uuid ";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\"><a href=\"rss_new_software.php\"><img src=\"images/feed-icon.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a>&nbsp;<a href=\"javascript://\" onclick=\"switchUl('f4');\">".__("Software detected in the last")." $days_software_detected ".__("Days").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f4');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f4\">\n";

  if ($myrow = mysql_fetch_array($result)){
    echo
      "<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td style=\"width:120px;\"><b>".__("IP Address")."</b></td>
           <td style=\"width:150px;\"><b>".__("Hostname")."</b></td>
           <td style=\"width:100px;\"><b>".__("Date Audited")."</b></td>
           <td><b>".__("Software")."</b></td>
         </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td><a href=\"system.php?pc=".$myrow["system_uuid"]."&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
           <td>" . return_date($myrow["software_first_timestamp"]) . "</td>
           <td>" . $myrow["software_name"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";
  } else {}

  echo
    "</div>
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>".__("Packages").": " . mysql_numrows($result) . "</b></td>
         </tr>
       </table>
     </div>";

} else {}


//if ($show_patches_not_detected == "y"){
/*
if (1 != 1){
  $sql = "SELECT count(ss_qno) as count, ss_qno from system_security, system WHERE (ss_status = 'NOT FOUND' OR ss_status = 'Warning') AND ss_timestamp = system_timestamp AND ss_uuid = system_uuid group by ss_qno order by count DESC LIMIT " . $number_patches_not_detected;
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\"><a href=\"javascript://\" onclick=\"switchUl('f5');\">$l_top  $number_patches_not_detected $l_mis.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f5');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>
     <div style=\"display:none;\" id=\"f5\">\n";

  if ($myrow = mysql_fetch_array($result)){
    echo
      "<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td style=\"width:150px;\" align=\"center\"><b>$l_pcw</b></td>
           <td style=\"width:150px;\" align=\"center\"><b>$l_qno</b></td>
           <td style=\"width:150px;\" align=\"center\"><b>$l_lin</b></td>
         </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td align=\"center\"><a href=\"list_missing_patches.php?sub=sw1&amp;name=" . url_clean($myrow["ss_qno"]) . "\" >" . $myrow["count"] . "</a></td>
           <td align=\"center\"><a href=\"list_missing_patches.php?sub=sw2&amp;name=" . url_clean($myrow["ss_qno"]) . "\" >" . $myrow["ss_qno"] . "</a></td>
           <td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . $myrow["ss_qno"] . "%22&amp;btnG=Search\"><img border=0 alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" /></a></td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
  echo "</div>\n</div>";
} else {}
*/



if ($show_detected_servers == "y"){


  //// WEB Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";
  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
          <tr>
            <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f6');\">".__("WEB Servers").".</a></td>
            <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f6');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
          </tr>
        </table>

        <div style=\"display:none;\" id=\"f6\">
          <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
            <tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
            </tr>\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address FROM service ser, system sys
           WHERE (ser.service_display_name LIKE 'IIS Admin%' OR ser.service_display_name LIKE 'Apache%') AND
           ser.service_uuid = sys.system_uuid AND
           ser.service_timestamp = sys.system_timestamp ORDER BY system_name";

  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected
    echo
      "<tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("State")."</b></td>
       </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href= \"http://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}
  // Nmap discovered on Audited PC
  $sql = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number  from system sys, nmap_ports port where port.nmap_port_number = '80' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
       </tr>\n";

    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td></td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}
  // Nmap discovered on Other equipment
  $sql = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number  from other oth, nmap_ports port where (port.nmap_port_number = '80' OR port.nmap_port_number = '443') AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td>
       </tr>

       <tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         <td></td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}

  echo
        "</table>
       </div>

       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>".__("Systems").": " . $count . "</b></td>
         </tr>
       </table>
     </div>\n";













  //// FTP Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f7');\">".__("FTP Servers").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f7');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
        </table>

        <div style=\"display:none;\" id=\"f7\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system
          WHERE service_display_name LIKE 'FTP%' AND service_uuid = system_uuid AND service_timestamp = system_timestamp
          ORDER BY system_name";
  $result = mysql_query($sql, $db);

  if ($myrow = mysql_fetch_array($result)){
    // Service Detected

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("State")."</b></td>
           </tr>\n";

    do {$bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href=\"ftp://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}


  // Nmap discovered on Audited PC
  $sql = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number from system sys, nmap_ports port where port.nmap_port_number = '21' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td>&nbsp;</td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}

  // Nmap discovered on Other equipment
  $sql = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '21' AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);

  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td>
       </tr>

       <tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
//<td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         
    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td><a href=\"ftp://".$myrow["other_network_name"]."\" onclick=\"this.target='_blank';\" /a></td>
         <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         <td></td>
         <td></td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}
  echo
    "
                        </table>
                </div>

        <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>".__("Systems").": " . $count . "</b></td>
         </tr>
       </table>
     </div>\n";












  //// Telnet Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f8');\">".__("Telnet Servers")."</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f8');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f8\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system
          WHERE service_display_name = 'Telnet' AND service_started = 'True' AND service_timestamp = system_timestamp
          AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected and Started

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("State")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href=\"telnet://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}

  // Nmap discovered on Audited PC
  $sql = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number from system sys, nmap_ports port where port.nmap_port_number = '23' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><a href=\"telnet://".$myrow["system_name"]."\" onclick=\"this.target='_blank';\" />" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '23' AND port.nmap_other_id = oth.other_id";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td>
       </tr>

       <tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><a href=\"telnet://".$myrow["other_network_name"]."\" onclick=\"this.target='_blank';\" />" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         <td></td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}

    echo
          "
                  </table>
                  </div>

         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>".__("Systems").": $count </b></td>
           </tr>
         </table>
       </div>\n";











  //// Email Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f9');\">".__("Email Servers")."</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f9');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f9\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system WHERE
           (service_display_name = 'Microsoft Exchange Information Store' OR
           service_display_name = 'Simple Mail Transport Protocol (SMTP)' OR
           service_display_name LIKE '%Lotus%Domino%' OR
           service_display_name = 'Simple Mail Transfer Protocol (SMTP)')
           AND service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected
        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("State")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href=\"telnet://".$myrow["system_name"].":25\" onclick=\"this.target='_blank';\" />" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td colspan=\"4\"><br />&nbsp;</td></tr>\n";

  } else {}

  // Nmap discovered on Audited PC
  $sql = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number from system sys, nmap_ports port where port.nmap_port_number = '25' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><a href=\"telnet://".$myrow["system_name"].":25\" onclick=\"this.target='_blank';\" />" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '25' AND port.nmap_other_id = oth.other_id";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td>
       </tr>

       <tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td><a href=\"telnet://".$myrow["other_network_name"].":25\" onclick=\"this.target='_blank';\" />" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=summary\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}

  echo
          "</table>
         </div>

         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>".__("Systems").": " . $count . "</b></td>
           </tr>
         </table>
       </div>\n";









  //// VNC Servers Detected
  // if ($show_detected_vnc == "y"){
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f10');\">".__("VNC Servers")."</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f10');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f10\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system WHERE
           service_display_name LIKE '%VNC%' AND
           service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("State")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href=\"launch.php?hostname=".$myrow["system_name"]."&amp;application=vnc&amp;ext=vnc\"/>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Audited PC
  $sql = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number from system sys, nmap_ports port where port.nmap_port_number = '5900' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '5900' AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td>
       </tr>

       <tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=summary\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}

  echo
          "</table>
         </div>

         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>".__("Systems").": " . $count . "</b></td>
           </tr>
         </table>
       </div>\n";


} else {}
//











  //// Terminal Services Servers Detected
  if (isset($show_detected_rdp) AND $show_detected_rdp == "y"){
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f11');\">".__("RDP and Terminal Servers").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f11');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f11\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system WHERE
           service_display_name LIKE '%Terminal Services%' AND
           service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("State")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href=\"launch.php?hostname=".$myrow["system_name"]."&amp;application=rdp&amp;ext=rdp\"/>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Audited PC
  $sql = "select sys.net_ip_address,sys.system_name,sys.system_uuid, port.nmap_port_number from system sys, nmap_ports port where port.nmap_port_number = '3389' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;application=rdp&amp;ext=rdp\"/>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td ><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
            </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number from other oth, nmap_ports port where port.nmap_port_number = '3389' AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Other equipment")."</b></td>
       </tr>

       <tr style=\"bgcolor:" . $bgcolor . ";\">
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("Port")."</b></td>
         <td></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td ><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}

  echo
          "</table>
         </div>

         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>".__("Systems").": " . $count . "</b></td>
           </tr>
         </table>
       </div>\n";

} else {}


//



//// XP SP2 without up to date AV
if (isset($show_detected_xp_av) AND $show_detected_xp_av == "y"){
  $sql = "SELECT system_name, net_ip_address, system_uuid, virus_name, virus_uptodate FROM system WHERE (virus_name = '' OR virus_uptodate = 'False') AND system_service_pack = '2.0' AND system_os_name LIKE 'Microsoft Windows XP%' ORDER BY system_name";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">
         <tr>
           <td class=\"indexheadlines\" colspan=\"3\"><a href=\"javascript://\" onclick=\"switchUl('f12');\">".__("XP SP2 without up to date AntiVirus")."</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f12');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f12\">";

  if ($myrow = mysql_fetch_array($result)){


    echo
      "<table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td><b>".__("IP Address")."</b></td>
           <td><b>".__("Hostname")."</b></td>
           <td><b>".__("AntiVirus Program")."</b></td>
           <td><b>".__("AntiVirus Up To Date")."</b></td>
         </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a></td>
           <td>" . $myrow["virus_name"] . "</td>
           <td>" . $myrow["virus_uptodate"] . "</td>
         </tr>";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "</table>";

  } else {}

  echo
    "</div>
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr><td colspan=\"3\"><b>".__("Systems").": " . mysql_numrows($result) . "</b></td></tr>
       </table>
     </div>";
} else {}



echo "</td>\n";



include "include_right_column.php";

?>

