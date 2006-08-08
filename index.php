<?php 
$page = "";
$extra = "";
$software = "";
$count = 0;
$total_rows = 0;



if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
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

if ($show_other_discovered == "y") {
  $sql  = "SELECT * FROM other WHERE (other_mac_address <> '' AND ";
  $sql .= "other_first_timestamp > '" . adjustdate(0,0,-$other_detected) . "') ORDER BY other_ip_address";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";
  echo 
    "<div class=\"main_each\">
      <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
        <tr>
          <td class=\"contenthead\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f0');\">$l_oti $other_detected $l_day.</a></td>
          <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f0');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
        </tr>
      </table>
    
      <div style=\"display:none;\" id=\"f0\">\n";

  if ($myrow = mysql_fetch_array($result)){
    echo 
    "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
       <tr>
         <td width=\"150\"><b>$l_ipa</b></td>
         <td width=\"150\"><b>$l_nam</b></td>
         <td width=\"100\"><b>$l_typ</b></td>
         <td width=\"250\"><b>$l_des</b></td>
       </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td><a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["other_type"] . "&nbsp;</td>
           <td>" . $myrow["other_description"] . "&nbsp;&nbsp;&nbsp;</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
    $total_rows = mysql_num_rows($result);
  } else {}
  
  echo
    "</div>
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr><td colspan=\"3\"><b>$l_oth: " . $total_rows . "</b></td></tr>
        </table>
    </div>\n";

} else {}


if ($show_system_discovered == "y") {
  $sql  = "SELECT system_name, net_ip_address, system_uuid, system_first_timestamp FROM system ";
  $sql .= "WHERE system_first_timestamp > '" . adjustdate(0,0,-$system_detected) . "000000' ORDER BY system_name";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
         <tr>
           <td class=\"contenthead\" colspan=\"3\"><a href=\"javascript://\" onclick=\"switchUl('f1');\">$l_syd $system_detected $l_day.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f1');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
          </tr>
       </table>
    
       <div style=\"display:none;\" id=\"f1\">\n";

  if ($myrow = mysql_fetch_array($result)){

    echo
        "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
           <tr>
             <td><b>$l_ipa</b></td>
             <td align=\"center\"><b>$l_sys</b></td>
             <td align=\"center\"><b>$l_ddt</b></td>
           </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td align=\"center\"><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "\">" . $myrow["system_name"] . "</a></td>
           <td align=\"center\">" . return_date_time($myrow["system_first_timestamp"]) . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
  echo
    "</div>
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>$l_syt: " . mysql_numrows($result) . "</b></td>
         </tr>
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
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">
         <tr>
           <td class=\"contenthead\" colspan=\"3\"><a href=\"javascript://\" onclick=\"switchUl('f2');\">$l_syn $days_systems_not_audited $l_day.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f2');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
        
       <div style=\"display:none;\" id=\"f2\">\n";

  if ($myrow = mysql_fetch_array($result)){

    echo 
      "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td><b>$l_ipa</b></td>
           <td align=\"center\"><b>$l_sys</b></td>
           <td align=\"center\"><b>$l_lau</b></td>
         </tr>\n";

    do {
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
  
    echo
      "<tr bgcolor=\"" . $bgcolor . "\">
         <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td align=\"center\"><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "\">" . $myrow["system_name"] . "</a></td>
           <td align=\"center\">" . return_date_time($myrow["system_timestamp"]) . "</td>
         </tr>\n";
        
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
  
  echo 
    "</div>
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>$l_syt: " . mysql_numrows($result) . "</b></td>
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
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td class=\"contenthead\"><a href=\"javascript://\" onclick=\"switchUl('f3');\">$l_par $partition_free_space MB.</a></td>
             <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f3');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
           </tr>
         </table>
       <div style=\"display:none;\" id=\"f3\">";

    if ($myrow = mysql_fetch_array($result)){
      
      echo 
        "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td width=\"150\"><b>$l_ipa</b></td>
             <td width=\"150\"><b>$l_sys</b></td>
             <td width=\"130\"><b>$l_fre MB</b></td>
             <td width=\"130\"><b>$l_siz</b></td>
             <td width=\"120\"><b>$l_fre %</b></td>
             <td width=\"100\"><b>$l_let</b></td>
             <td width=\"150\"><b>$l_nam</b></td>
           </tr>\n";
        
      do {
        if ($myrow["partition_size"] <> 0){ 
          $percent_free = round((($myrow["partition_free_space"] / $myrow["partition_size"]) * 100),1);
        } else {
          $percent_free = 0;
        }
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        echo 
          "<tr bgcolor=\"" . $bgcolor . "\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
             <td><a href=\"system_summary.php?pc=" . $myrow["partition_uuid"] . "\">" . $myrow["system_name"] . "</a></td>
             <td>" . $myrow["partition_free_space"] . " Mb</td>
             <td>" . $myrow["partition_size"] . " Mb</td>
             <td>" . $percent_free . " %</td>
             <td>" . $myrow["partition_caption"] . " </td>
             <td>" . $myrow["partition_volume_name"] . " </td>
           </tr>\n";
            
      } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {} 
  
  echo
    "</div>
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>$l_tpa: " . mysql_numrows($result) . "</b></td>
         </tr>
       </table>
     </div>\n";

} else {}

if ($show_software_detected == "y"){
  $sql  = "SELECT sw.software_name, sw.software_first_timestamp, sys.system_name, sys.system_uuid, ";
  $sql .= "sys.net_ip_address FROM software sw, system sys WHERE ";
  $sql .= "software_first_timestamp >= '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
  $sql .= "AND sys.system_first_timestamp < '" . adjustdate(0,0,-$days_software_detected) . "000000' ";
  $sql .= "AND software_name NOT LIKE '%Hotfix%' AND software_name NOT LIKE '%Update%' AND ";
  $sql .= "sw.software_uuid = sys.system_uuid";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"contenthead\"><a href=\"javascript://\" onclick=\"switchUl('f4');\">$l_sof $days_software_detected $l_day.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f4');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
  
       <div style=\"display:none;\" id=\"f4\">\n";

  if ($myrow = mysql_fetch_array($result)){
    echo
      "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td width=\"120\"><b>$l_ipa</b></td>
           <td width=\"150\"><b>$l_sys</b></td>
           <td width=\"100\"><b>$l_dat</b></td>
           <td><b>$l_swf</b></td>
         </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "\">" . $myrow["system_name"] . "</a></td>
           <td>" . return_date($myrow["software_first_timestamp"]) . "</td>
           <td>" . $myrow["software_name"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}

  echo 
    "</div>
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>$l_tpk: " . mysql_numrows($result) . "</b></td>
         </tr>
       </table>
     </div>";

} else {}
  
  
//if ($show_patches_not_detected == "y"){
if (1 != 1){
  $sql = "SELECT count(ss_qno) as count, ss_qno from system_security, system WHERE (ss_status = 'NOT FOUND' OR ss_status = 'Warning') AND ss_timestamp = system_timestamp AND ss_uuid = system_uuid group by ss_qno order by count DESC LIMIT " . $number_patches_not_detected;
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";
  
  echo 
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"contenthead\"><a href=\"javascript://\" onclick=\"switchUl('f5');\">$l_top  $number_patches_not_detected $l_mis.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f5');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
     <div style=\"display:none;\" id=\"f5\">\n";

  if ($myrow = mysql_fetch_array($result)){
    echo 
      "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td width=\"150\" align=\"center\"><b>$l_pcw</b></td>
           <td width=\"150\" align=\"center\"><b>$l_qno</b></td>
           <td width=\"150\" align=\"center\"><b>$l_lin</b></td>
         </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td align=\"center\"><a href=\"list_missing_patches.php?sub=sw1&amp;name=" . url_clean($myrow["ss_qno"]) . "\" >" . $myrow["count"] . "</a></td>
           <td align=\"center\"><a href=\"list_missing_patches.php?sub=sw2&amp;name=" . url_clean($myrow["ss_qno"]) . "\" >" . $myrow["ss_qno"] . "</a></td>
           <td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . $myrow["ss_qno"] . "%22&amp;btnG=Search\"><img border=0 alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" /></a></td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
  echo "</div>\n</div>";
} else {}




if ($show_detected_servers == "y"){








  //// WEB Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";
  echo 
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
          <tr>
            <td class=\"contenthead\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f6');\">$l_web.</a></td>
            <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f6');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
          </tr>
        </table>
        
        <div style=\"display:none;\" id=\"f6\">
          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
            <tr>
              <td colspan=\"2\"><b>$l_sev</b></td>
            </tr>\n";

  $sql  = "SELECT DISTINCT ser.service_uuid, ser.service_display_name, ser.service_started, sys.system_name, sys.net_ip_address FROM service ser, system sys 
           WHERE (ser.service_display_name LIKE 'IIS Admin%' OR ser.service_display_name LIKE 'Apache%') AND 
           ser.service_started = 'True' AND ser.service_uuid = sys.system_uuid AND 
           ser.service_timestamp = sys.system_timestamp ORDER BY system_name";

  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected
    echo 
      "<tr>
         <td><b>$l_ipa</b></td>
         <td><b>$l_nam</b></td>
         <td><b>$l_ser</b></td>
         <td><b>$l_ruo</b></td>
       </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system_summary.php?pc=" . $myrow["service_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href= http://".$myrow["system_name"]." TARGET=\"_blank\"/>" . $myrow["service_started"] . "</td>
         </tr>\n";
      
    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td><br />&nbsp;</td></tr>\n";
  } else {}
  // Nmap discovered on Audited PC
  $sql = "select DISTINCT sys.net_ip_address,sys.system_name,sys.system_uuid from system sys, nmap_ports port where port.nmap_port_number = '80' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_nma</b></td>
       </tr>
        
       <tr>
         <td width=\"150\"><b>$l_ipa</b></td>
         <td width=\"150\"><b>$l_nam</b></td>
       </tr>\n";
        
    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    
        echo
          "<tr bgcolor=\"" . $bgcolor . "\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td></td>
             <td></td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td><br />&nbsp;</td></tr>\n";
  } else {}
  // Nmap discovered on Other equipment
  $sql = "select DISTINCT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address from other oth, nmap_ports port where port.nmap_port_number = '80' AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_nmb</b></td>
       </tr>
      
       <tr bgcolor=\"" . $bgcolor . "\">
         <td width=\"150\"><b>$l_ipa</b></td>
         <td width=\"150\"><b>$l_nam</b></td>
         <td></td>
         <td></td>
       </tr>\n";
        
    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo 
      "<tr bgcolor=\"" . $bgcolor . "\">
         <td>" . ip_trans($myrow["other_ip"]) . "&nbsp;</td>
         <td><a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td></td>
         <td></td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    echo "<tr><td><br />&nbsp;</td></tr>\n";
  } else {}

  echo 
        "</table>
       </div>
  
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>$l_wed: " . $count . "</b></td>
         </tr>
       </table>
     </div>\n";













  //// FTP Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"contenthead\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f7');\">$l_ftp.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f7');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
        </table>
    
        <div style=\"display:none;\" id=\"f7\">
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system 
          WHERE service_display_name = 'FTP Publishing Service' AND service_uuid = system_uuid AND service_timestamp = system_timestamp 
          ORDER BY system_name";
  $result = mysql_query($sql, $db);

  if ($myrow = mysql_fetch_array($result)){
    // Service Detected
    
	echo 
           "<tr>
             <td colspan=\"2\"><b>$l_sev</b></td>
           </tr>
    
           <tr>
             <td><b>$l_ipa</b></td>
             <td><b>$l_nam</b></td>
             <td><b>$l_ser</b></td>
             <td><b>$l_ruo</b></td>
           </tr>\n";

    do {$bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;
      
      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system_summary.php?pc=" . $myrow["service_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href= ftp://".$myrow["system_name"]." TARGET=\"_blank\"/>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
  } else {}

  
  // Nmap discovered on Audited PC
  $sql = "select DISTINCT sys.net_ip_address,sys.system_name,sys.system_uuid from system sys, nmap_ports port where port.nmap_port_number = '21' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_nma</b></td>
       </tr>
    
       <tr>
         <td width=\"150\"><b>$l_ipa</b></td>
         <td width=\"150\"><b>$l_nam</b></td>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
       </tr>\n";
            
    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        
        echo
          "<tr bgcolor=\"" . $bgcolor . "\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td></td>
             <td>&nbsp;</td>
           </tr>\n";
        
    } while ($myrow = mysql_fetch_array($result));
  } else {}
  
  // Nmap discovered on Other equipment
  $sql = "select DISTINCT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address from other oth, nmap_ports port where port.nmap_port_number = '21' AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);

  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    
    echo
		"<tr>
           <td colspan=\"2\"><b>$l_nmb</b></td>
         </tr>

         <tr bgcolor=\"" . $bgcolor . "\">
           <td width=\"150\"><b>$l_ipa</b></td>
           <td width=\"150\"><a href= ftp://".$myrow["system_name"]." TARGET=\"_blank\"/><b>$l_nam</b></td>
           <td>&nbsp;</td>
           <td>&nbsp;</td>
         </tr>\n";
    do {
        $count = $count + 1;
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
        
        echo 
          "<tr bgcolor=\"" . $bgcolor . "\">
             <td>" . ip_trans($myrow["other_ip"]) . "&nbsp;</td>
             <td><a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo 
      "<tr>
         <td><br />&nbsp;</td>
       </tr>\n";

  } else {}
  echo 
    "
			</table>
		</div>
	
	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td colspan=\"3\"><b>$l_fta: " . $count . "</b></td>
         </tr>
       </table>
     </div>\n";












  //// Telnet Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"contenthead\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f8');\">$l_ats.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f8');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
        
       <div style=\"display:none;\" id=\"f8\">
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system 
          WHERE service_display_name = 'Telnet' AND service_started = 'True' AND service_timestamp = system_timestamp 
          AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected and Started
    echo 
      "<tr>
         <td colspan=\"4\"><b>Service Detected and Started</b></td>
       </tr>
    
       <tr>
         <td><b>$l_ipa</b></td>
         <td><b>$l_nam</b></td>
         <td><b>$l_ser</b></td>
         <td><b>$l_ruo</b></td>
       </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system_summary.php?pc=" . $myrow["service_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>\
           <td><a href= telnet://".$myrow["system_name"]." TARGET=\"_blank\"/>" . $myrow["service_started"] . "</td>\
         </tr>\n";
        
    } while ($myrow2 = mysql_fetch_array($result));
  } else {}

  // Nmap discovered on Audited PC
  $sql = "select DISTINCT sys.net_ip_address,sys.system_name,sys.system_uuid from system sys, nmap_ports port where port.nmap_port_number = '23' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo
      "<tr>
         <td colspan=\"2\"><b>$l_nma</b></td>
       </tr>
    
       <tr>
         <td width=\"150\"><b>$l_ipa</b></td>
         <td width=\"150\"><b>$l_nam</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo 
      "<tr bgcolor=\"" . $bgcolor . "\">
         <td><a href= telnet://".$myrow["system_name"]." TARGET=\"_blank\"/>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
         <td><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td></td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select DISTINCT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address from other oth, nmap_ports port where port.nmap_port_number = '23' AND port.nmap_other_id = oth.other_id";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    
    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_nmb</b></td>
       </tr>

       <tr bgcolor=\"" . $bgcolor . "\">
         <td width=\"150\"><b>$l_ipa</b></td>
         <td colspan=\"3\"><b>$l_nam</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    
    echo
      "<tr bgcolor=\"" . $bgcolor . "\">
         <td><a href= telnet://".$myrow["other_network_name"]." TARGET=\"_blank\"/>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
         <td><a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         <td></td>
         <td></td>
       </tr>\n";
        
    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  
    echo 
          "
		  </table>
		  </div>
         
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>$l_tel: $count </b></td>
           </tr>
         </table>
       </div>\n";











  //// Email Servers Detected
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo 
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"contenthead\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f9');\">$l_eml.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f9');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
  
       <div style=\"display:none;\" id=\"f9\">
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system WHERE 
           (service_display_name = 'Microsoft Exchange Information Store' OR 
           service_display_name = 'Simple Mail Transport Protocol (SMTP)' OR 
           service_display_name = 'Simple Mail Transfer Protocol (SMTP)')
           AND service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected
    echo
      "<tr>
         <td colspan=\"2\"><b>$l_sev</b></td>
       </tr>
    
       <tr>
         <td><b>$l_ipa</b></td>
         <td><b>$l_nam</b></td>
         <td><b>$l_ser</b></td>
         <td><b>$l_ruo</b></td>
       </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system_summary.php?pc=" . $myrow["service_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href= telnet://".$myrow["system_name"].":25 TARGET=\"_blank\"/>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td colspan=\"4\"><br />&nbsp;</td></tr>\n";

  } else {}

  // Nmap discovered on Audited PC
  $sql = "select DISTINCT sys.net_ip_address,sys.system_name,sys.system_uuid from system sys, nmap_ports port where port.nmap_port_number = '25' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){

    echo 
      "<tr>
         <td colspan=\"4\"><b>$l_nma</b></td>
       </tr>

       <tr>
         <td width=\"150\"><b>$l_ipa</b></td>
         <td colspan=\"3\"><b>$l_nam</b></td>
       </tr>\n";
    
    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
    echo 
      "<tr bgcolor=\"" . $bgcolor . "\">
         <td><a href= telnet://".$myrow["system_name"].":25 TARGET=\"_blank\"/>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
         <td colspan=\"3\"><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
       </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select DISTINCT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address from other oth, nmap_ports port where port.nmap_port_number = '25' AND port.nmap_other_id = oth.other_id";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
      "<tr>
         <td colspan=\"2\"><b>$l_nmb</b></td>
       </tr>
        
       <tr bgcolor=\"" . $bgcolor . "\">
         <td width=\"150\"><b>$l_ipa</b></td>
         <td colspan=\"3\"><b>$l_nam</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

    echo
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td><a href= telnet://".$myrow["other_network_name"].":25 TARGET=\"_blank\"/>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td colspan=\"3\"><a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  
  echo 
          "</table>
         </div>
  
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>$l_emm: " . $count . "</b></td>
           </tr>
         </table>
       </div>\n";









  //// VNC Servers Detected
  // if ($show_detected_vnc == "y"){
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo 
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"contenthead\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f10');\">$l_avs.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f10');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
  
       <div style=\"display:none;\" id=\"f10\">
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system WHERE 
           service_display_name LIKE '%VNC%' AND service_started = 'True' AND 
           service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected
    
    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_sev</b></td>
       </tr>
    
       <tr>
         <td><b>$l_ipa</b></td>
         <td><b>$l_nam</b></td>
         <td><b>$l_ser</b></td>
         <td><b>$l_ruo</b></td>
       </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;
      
      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system_summary.php?pc=" . $myrow["service_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href= http://".$myrow["system_name"].":5800 TARGET=\"_blank\"/>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Audited PC
  $sql = "select DISTINCT sys.net_ip_address,sys.system_name,sys.system_uuid from system sys, nmap_ports port where port.nmap_port_number = '5900' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
  
    echo
      "<tr>
         <td colspan=\"2\"><b>$l_nma</b></td>
       </tr>
        
       <tr>
         <td width=\"150\"><b>$l_ipa</b></td>
         <td colspan=\"3\"><b>$l_nam</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
        echo
          "<tr bgcolor=\"" . $bgcolor . "\">
             <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td colspan=\"3\"><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select DISTINCT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address from other oth, nmap_ports port where port.nmap_port_number = '5900' AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    
    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_nmb</b></td>
       </tr>
    
       <tr bgcolor=\"" . $bgcolor . "\">
         <td width=\"150\"><b>$l_ipa</b></td>
         <td colspan=\"3\"><b>$l_nam</b></td>
       </tr>\n";
    
    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td colspan=\"3\"><a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         </tr>\n";
      
    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}

  echo 
          "</table>
         </div>
  
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>$l_vnc: " . $count . "</b></td>
           </tr>
         </table>
       </div>\n";


} else {}
//

  //// Terminal Services Servers Detected
  if ($show_detected_rdp == "y"){
  $count = 0;
  $bgcolor = "#FFFFFF";

  echo 
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td class=\"contenthead\" colspan=\"4\"><a href=\"javascript://\" onclick=\"switchUl('f11');\">$l_ardp.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f11');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
  
       <div style=\"display:none;\" id=\"f11\">
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";

  $sql  = "SELECT DISTINCT service_uuid, service_display_name, service_started, system_name, net_ip_address FROM service, system WHERE 
           service_display_name LIKE '%Terminal Services%' AND service_started = 'True' AND 
           service_timestamp = system_timestamp AND service_uuid = system_uuid ORDER BY system_name";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    // Service Detected
    
    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_sev</b></td>
       </tr>
    
       <tr>
         <td><b>$l_ipa</b></td>
         <td><b>$l_nam</b></td>
         <td><b>$l_ser</b></td>
         <td><b>$l_ruo</b></td>
       </tr>\n";

    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      $count = $count + 1;
      
      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
           <td><a href=\"system_summary.php?pc=" . $myrow["service_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           <td>" . $myrow["service_display_name"] . "</td>
           <td><a href= \"launch_rdp.php?launch=".$myrow["system_name"].".rdp\"/>" . $myrow["service_started"] . "</td>
         </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Audited PC
  $sql = "select DISTINCT sys.net_ip_address,sys.system_name,sys.system_uuid from system sys, nmap_ports port where port.nmap_port_number = '3389' AND port.nmap_other_id = sys.system_uuid";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
  
    echo
      "<tr>
         <td colspan=\"2\"><b>$l_nma</b></td>
       </tr>
        
       <tr>
         <td width=\"150\"><b>$l_ipa</b></td>
         <td colspan=\"3\"><b>$l_nam</b></td>
       </tr>\n";

    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
        echo
          "<tr bgcolor=\"" . $bgcolor . "\">
             <td><a href= \"launch_rdp.php?launch=".$myrow["system_name"].".rdp\"/>" . ip_trans($myrow["net_ip_address"]) . "&nbsp;</td>
             <td colspan=\"3\"><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "&amp;sub=1\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
           </tr>\n";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}
  // Nmap discovered on Other equipment
  $sql = "select DISTINCT oth.other_id, oth.other_ip_address, oth.other_network_name, oth.other_mac_address from other oth, nmap_ports port where port.nmap_port_number = '3389' AND port.nmap_other_id = oth.other_mac_address";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    
    echo 
      "<tr>
         <td colspan=\"2\"><b>$l_nmb</b></td>
       </tr>
    
       <tr bgcolor=\"" . $bgcolor . "\">
         <td width=\"150\"><b>$l_ipa</b></td>
         <td colspan=\"3\"><b>$l_nam</b></td>
       </tr>\n";
    
    do {
      $count = $count + 1;
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["other_ip_address"]) . "&nbsp;</td>
           <td colspan=\"3\"><a href=\"other_summary.php?other=" . $myrow["other_id"] . "&amp;sub=1\">" . $myrow["other_network_name"] . "</a>&nbsp;&nbsp;&nbsp;</td>
         </tr>\n";
      
    } while ($myrow = mysql_fetch_array($result));
    
    echo "<tr><td><br />&nbsp;</td></tr>\n";

  } else {}

  echo 
          "</table>
         </div>
  
         <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
           <tr>
             <td colspan=\"3\"><b>$l_rdp: " . $count . "</b></td>
           </tr>
         </table>
       </div>\n";

} else {}


//



//// XP SP2 without up to date AV
if ($show_detected_xp_av == "y"){
  $sql = "SELECT system_name, net_ip_address, system_uuid, virus_name, virus_uptodate FROM system WHERE (virus_name = '' OR virus_uptodate = 'False') AND system_service_pack = '2.0' AND system_os_name LIKE 'Microsoft Windows XP%' ORDER BY system_name";
  $result = mysql_query($sql, $db);
  $bgcolor = "#FFFFFF";

  echo
    "<div class=\"main_each\">
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">
         <tr>
           <td class=\"contenthead\" colspan=\"3\"><a href=\"javascript://\" onclick=\"switchUl('f12');\">$l_xps.</a></td>
           <td align=\"right\"><a href=\"javascript://\" onclick=\"switchUl('f12');\"><img src=\"images/down.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" /></a></td>
         </tr>
       </table>
  
       <div style=\"display:none;\" id=\"f12\">";
        
  if ($myrow = mysql_fetch_array($result)){
  
    echo 
      "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr>
           <td><b>$l_ipa</b></td>
           <td align=\"center\"><b>$l_sys</b></td>
           <td align=\"center\"><b>$l_anu</b></td>
           <td align=\"center\"><b>$l_anv</b></td>
         </tr>\n";
    
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      
      echo 
        "<tr bgcolor=\"" . $bgcolor . "\">
           <td>" . ip_trans($myrow["net_ip_address"]) . "</td>
           <td align=\"center\"><a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "\">" . $myrow["system_name"] . "</a></td>
           <td align=\"center\">" . $myrow["virus_name"] . "</td>
           <td align=\"center\">" . $myrow["virus_uptodate"] . "</td>
         </tr>";

    } while ($myrow = mysql_fetch_array($result));
    
    echo "</table>";

  } else {}

  echo
    "</div>
       <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >
         <tr><td colspan=\"3\"><b>Systems: " . mysql_numrows($result) . "</b></td></tr>
       </table>
     </div>";
} else {}



echo "</td>\n";

include "include_right_column.php";

include "include_png_replace.php";

echo "</body>\n</html>\n";

?>
