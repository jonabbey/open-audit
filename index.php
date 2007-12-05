<?php
/**
*
* @version $Id: index.php  24th May 2007
*
* @author The Open Audit Developer Team
* @objective Index Page for Open Audit.
* @package open-audit (www.open-audit.org)
* @copyright Copyright (C) open-audit.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see ../gpl.txt
* Open-Audit is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See www.open-audit.org for further copyright notices and details.
*
*/ 

$page = "";
$extra = "";
$software = "";
$count = 0;
$total_rows = 0;
$latest_version = "07.12.09";

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
  $sql .= "WHERE system_first_timestamp > '" . adjustdate(0,0,-$system_detected) . "000000' ";
  $sql .= "ORDER BY system_name";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
         <tr>
           <td class=\"indexheadlines\" colspan=\"3\"><a href=\"rss_new_systems.php\"><img src=\"images/feed-icon.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a>&nbsp;<a href=\"javascript://\" onclick=\"switchUl('f0');\">".__("Systems Discovered in the last")." $system_detected ".__("Days").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f0');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
          </tr>
       </table>

       <div style=\"display:none;\" id=\"f0\">\n";

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
  $sql  = "SELECT * FROM other ";
  $sql .= "WHERE (other_ip_address <> '' AND other_first_timestamp > '" . adjustdate(0,0,-$other_detected) . "000000') ";
  $sql .= "ORDER BY other_ip_address";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";
  echo
    "<div class=\"main_each\">
      <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
        <tr>
          <td class=\"indexheadlines\" colspan=\"3\"><a href=\"rss_new_other.php\"><img src=\"images/feed-icon.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a>&nbsp;<a href=\"javascript://\" onclick=\"switchUl('f1');\">".__("Other Items Discovered in the last")." $other_detected ".__("Days").".</a></td>
          <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f1');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
        </tr>
      </table>

      <div style=\"display:none;\" id=\"f1\">\n";

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
  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_timestamp FROM system ";
  $sql .= "WHERE system_timestamp < '" . adjustdate(0,0,-$days_systems_not_audited) . "000000' ";
  $sql .= "ORDER BY system_name";
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
  $sql  = "SELECT sys.system_name, sys.net_ip_address, par.partition_uuid, par.partition_volume_name, par.partition_caption, par.partition_free_space, par.partition_size, par.partition_timestamp ";
  $sql .= "FROM system sys, partition par ";
  $sql .= "WHERE par.partition_free_space < '$partition_free_space' AND sys.system_uuid = par.partition_uuid AND par.partition_timestamp = sys.system_timestamp ";
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
             <td style=\"width:150px;\"><b>".__("Size")." ".__("MB")."</b></td>
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
             <td>" . $myrow["partition_free_space"] . " MB</td>
             <td>" . $myrow["partition_size"] . " MB</td>
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
      $sql  = "SELECT sw.software_name, sw.software_first_timestamp, sys.system_name, sys.system_uuid, sys.net_ip_address ";
      $sql .= "FROM software sw, system sys ";
      $sql .= "WHERE sw.software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
      //$sql .= "WHERE software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
      $sql .= "AND sys.system_first_timestamp < '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
      $sql .= "AND sw.software_name NOT LIKE '%Hotfix%' AND sw.software_name NOT LIKE '%Update%' AND sw.software_name NOT LIKE '%Service Pack%' AND sw.software_name NOT REGEXP '[KB|Q][0-9]{6,}' ";
      $sql .= "AND sw.software_timestamp = sys.system_timestamp ";
      $sql .= "AND sw.software_uuid = sys.system_uuid ";
      $sql .= "ORDER BY sw.software_name";
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
                <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f5');\">".__("WEB Servers").".</a></td>
                <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f5');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
              </tr>
            </table>

            <div style=\"display:none;\" id=\"f5\">
              <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
                <tr>
                  <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
                </tr>\n";

      $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_name, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
      $sql .= "FROM service ser, system sys ";
      $sql .= "WHERE (ser.service_name = 'W3Svc' OR ser.service_name LIKE '%Apache%' OR ser.service_name LIKE 'Oracle%ServerProcessManager') ";
      $sql .= "AND ser.service_uuid = sys.system_uuid AND ser.service_timestamp = sys.system_timestamp ";
      $sql .= "ORDER BY system_name";

      $result = mysql_query($sql, $db);
      if ($myrow = mysql_fetch_array($result)){
        // Web Service Detected
        echo
          "<tr>
             <td><b>".__("IP Address")."</b></td>
             <td><b>".__("Hostname")."</b></td>
             <td><b>".__("Service")."</b></td>
             <td><b>".__("Started")."</b></td>
           </tr>\n";

        do {
          $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
          $count = $count + 1;

          echo
            "<tr style=\"bgcolor:" . $bgcolor . ";\">
               <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
               <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
               <td>" . $myrow["service_display_name"] . "</td>
               <td>" . $myrow["service_started"] . "</td>
             </tr>\n";

        } while ($myrow = mysql_fetch_array($result));
        echo "<tr><td>&nbsp;</td></tr>\n";
      } else {}
     
  // WS - Nmap discovered on Audited PC
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '80' OR port.nmap_port_number = '443') AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("TCP Port")."</b></td>
		 <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
		
        $app = "http";
	    if ($myrow["nmap_port_number"] <> "80") { $app = "https"; }
		
        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=".$app."\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
          </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}
  // WS - Nmap discovered on Other equipment
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '80' OR port.nmap_port_number = '443') AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
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
         <td><b>".__("TCP Port")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
	  
      $app = "http";
	  if ($myrow["nmap_port_number"] <> "80") { $app = "https"; } 
	
      echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=".$app."\"/>" . $myrow["nmap_port_number"] . "&nbsp;&nbsp;&nbsp;</td>
         <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
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
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f6');\">".__("FTP Servers").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f6');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
        </table>

        <div style=\"display:none;\" id=\"f6\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_display_name LIKE 'FTP%' AND ser.service_uuid = sys.system_uuid AND ser.service_timestamp = sys.system_timestamp ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);

  if ($myrow = mysql_fetch_array($result)){
    // FTP Service Detected

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("Started")."</b></td>
           </tr>\n";

    do {$bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}


  // FTP - Nmap discovered on Audited PC
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '21' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("TCP Port")."</b></td>
		 <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=ftp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}

  // FTP - Nmap discovered on Other equipment
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '21' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
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
         <td><b>".__("TCP Port")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
//<td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         
    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td ><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=ftp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
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
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f7');\">".__("Telnet Servers")."</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f7');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f7\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_display_name = 'Telnet' AND ser.service_started = 'True' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Telnet Service Detected and Started

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("Started")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td>&nbsp;</td></tr>\n";
  } else {}

  // Telnet - Nmap discovered on Audited PC
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '23' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("TCP Port")."</b></td>
		 <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>

       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Telnet - Nmap discovered on Other equipment
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '23' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
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
         <td><b>".__("TCP Port")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
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
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f8');\">".__("Email Servers")."</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f8');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f8\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE (ser.service_name = 'MSExchangeIS' OR ser.service_name = 'SMTPSvc' OR ser.service_display_name LIKE 'SMTP' OR ser.service_display_name LIKE '%Lotus%Domino%') ";
  $sql .= "AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Email - Service Detected
        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("Started")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td colspan=\"4\"><br />&nbsp;</td></tr>\n";

  } else {}

  // Email - Nmap discovered on Audited PC
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '25' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("TCP Port")."</b></td>
		 <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr style=\"bgcolor:" . $bgcolor . ";\">
         <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
         <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // Email - Nmap discovered on Other equipment
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '25' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
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
         <td><b>".__("TCP Port")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=telnet&amp;ext=vbs\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
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
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f9');\">".__("VNC Servers")."</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f9');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f9\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_name LIKE '%VNC%' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // VNC - Service Detected

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("Started")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // VNC - Nmap discovered on Audited PC
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '5900' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("TCP Port")."</b></td>
		 <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=".$vnc_type."_"."vnc&amp;ext=vnc\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // VNC - Nmap discovered on Other equipment
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '5900' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
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
         <td><b>".__("TCP Port")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=".$vnc_type."_"."vnc&amp;ext=vnc\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
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












  //// Terminal Services Servers Detected
  if (isset($show_detected_rdp) AND $show_detected_rdp == "y"){
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f10');\">".__("RDP and Terminal Servers").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f10');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f10\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE ser.service_name LIKE '%TermService%' AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // TS Service Detected

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("Started")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // TS - Nmap discovered on Audited PC
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '3389' AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("TCP Port")."</b></td>
		 <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td ><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td><a href= \"launch.php?hostname=".$myrow["system_name"]."&amp;domain=".$myrow["net_domain"]."&amp;application=rdp&amp;ext=rdp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
            </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // TS - Nmap discovered on Other equipment
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE port.nmap_port_number = '3389' AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
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
         <td><b>".__("TCP Port")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td ><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td><a href= \"launch_other.php?hostname=".$myrow["other_ip_address"]."&amp;application=rdp&amp;ext=rdp\"/>" . $myrow["nmap_port_number"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
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





  //// Database Servers Detected
  //if (isset($show_detected_db) AND $show_detected_db == "y"){
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"indexheadlines\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f11');\">".__("Database Servers").".</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f11');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" /></a></td>
         </tr>
       </table>

       <div style=\"display:none;\" id=\"f11\">
         <table style=\"border:0px;\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_name, ser.service_started, sys.system_name, sys.net_ip_address ";
  $sql .= "FROM service ser, system sys ";
  $sql .= "WHERE (ser.service_name LIKE '%MySql%' OR ser.service_name = 'MSSQLSERVER' OR ser.service_name LIKE 'MSSQL$%' OR ser.service_name LIKE 'Oracle%TNSListener' OR ser.service_name = 'DB2') AND ser.service_timestamp = sys.system_timestamp AND ser.service_uuid = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // DB Service Detected

        echo
           "<tr>
              <td colspan=\"2\"><b>".__("Windows")." ".__("Services")."</b></td>
           </tr>

           <tr>
            <td><b>".__("IP Address")."</b></td>
            <td><b>".__("Hostname")."</b></td>
            <td><b>".__("Service")."</b></td>
            <td><b>".__("Started")."</b></td>
           </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system.php?pc=" . $myrow["service_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // DB - Nmap discovered on Audited PC
  $sql  = "SELECT sys.net_ip_address, sys.system_name, sys.system_uuid, sys.net_domain, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM system sys, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '3306' OR port.nmap_port_number = '1433' OR port.nmap_port_number = '1521' OR port.nmap_port_number = '523') AND port.nmap_port_proto = 'tcp' AND port.nmap_other_id = sys.system_uuid ";
  $sql .= "ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"4\"><b>".__("Nmap discovered on Audited PC")."</b></td>
       </tr>

       <tr>
         <td><b>".__("IP Address")."</b></td>
         <td><b>".__("Hostname")."</b></td>
         <td><b>".__("TCP Port")."</b></td>
		 <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo
          "<tr style=\"bgcolor:" . $bgcolor . ";\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td ><a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
             <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
            </tr>\n";

    } while ($myrow = mysql_fetch_array($result));

    echo "<tr><td>&nbsp;</td></tr>\n";

  } else {}
  // DB - Nmap discovered on Other equipment
  $sql  = "SELECT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address, port.nmap_port_number, port.nmap_port_proto, port.nmap_port_name, port.nmap_port_version ";
  $sql .= "FROM other oth, nmap_ports port ";
  $sql .= "WHERE (port.nmap_port_number = '3306' OR port.nmap_port_number = '1433' OR port.nmap_port_number = '1521' OR port.nmap_port_number = '523') AND port.nmap_port_proto = 'tcp' AND (port.nmap_other_id = oth.other_mac_address OR port.nmap_other_id = oth.other_id) ";
  $sql .= "ORDER BY oth.other_network_name";
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
         <td><b>".__("TCP Port")."</b></td>
         <td><b>".__("Service")."</b></td>
         <td><b>".__("Version")."</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo
        "<tr style=\"bgcolor:" . $bgcolor . ";\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td ><a href=\"system.php?other=" . $myrow["other_id"] . "&amp;view=other_system\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["nmap_port_number"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_name"] . "&nbsp;</td>
           <td>" . $myrow["nmap_port_version"] . "&nbsp;</td>
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


// End show detected servers




//// XP SP2 without up to date AV
if (isset($show_detected_xp_av) AND $show_detected_xp_av == "y"){
  $sql  = "SELECT system_name, net_ip_address, system_uuid, virus_name, virus_uptodate FROM system ";
  $sql .= "WHERE (virus_name = '' OR virus_uptodate = 'False') AND system_service_pack = '2.0' AND system_os_name LIKE 'Microsoft Windows XP%' ";
  $sql .= "ORDER BY system_name";
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

