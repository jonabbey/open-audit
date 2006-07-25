<?php 
$page = "";
$extra = "";
include "include.php";
echo "<td valign=\"top\">\n";

if (isset($_GET['show_all'])){ $count_system = '10000'; } else {}
if (isset($_GET['page_count'])){ $page_count = $_GET['page_count']; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

if ($sub <> "sw1"){
  if (isset($_GET['sort'])){ $sort = $_GET['sort']; } else { $sort = 'software_name';}
  
  $sql = "SELECT count(software_name) AS software_count, software_name, software_version, software_publisher FROM software, system where software_name NOT LIKE '%hotfix%' AND software_name NOT LIKE '%update%' AND software_name NOT LIKE '%Service Pack%' AND software_uuid = system_uuid AND software_timestamp = system_timestamp GROUP BY software_name, software_version ORDER BY " . $sort . ", software_name, software_version";
  $result = mysql_query($sql);
  $total_current_software = mysql_num_rows($result);
  
  $sql = "SELECT count(software_name) AS software_count, software_name, software_version, software_publisher FROM software where software_name NOT LIKE '%hotfix%' AND software_name NOT LIKE '%update%' AND software_name NOT LIKE '%Service Pack%' GROUP BY software_name, software_version ORDER BY " . $sort . ", software_name, software_version";
  $result = mysql_query($sql);
  $total_software = mysql_num_rows($result);
  
  $sql = "SELECT count(software_name) AS software_count, software_name, software_version, software_publisher FROM software, system where software_name NOT LIKE '%hotfix%' AND software_name NOT LIKE '%update%' AND software_name NOT LIKE '%Service Pack%' AND software_uuid = system_uuid AND software_timestamp = system_timestamp GROUP BY software_name, software_version ORDER BY " . $sort . ", software_name, software_version LIMIT " . $page_count . "," . $count_system;
  $result = mysql_query($sql);

  echo "<div class=\"main_each\">\n";
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo " <tr>\n  <td align=\"left\" class=\"contenthead\" colspan=\"4\">$l_lic<br />&nbsp;</td>\n";
  include "include_list_buttons.php";
  echo " </tr>\n";
  if ($myrow = mysql_fetch_array($result)){
    echo " <tr>\n";
    echo "  <td align=\"center\"><a href=\"" . $_SERVER["PHP_SELF"] . "?sub=" . $sub . "&amp;page_count=" . $page_current . "&amp;sort=software_count\">$l_cnt</a>&nbsp;</td>\n";
    echo "  <td><a href=\"" . $_SERVER["PHP_SELF"] . "?sub=" . $sub . "&amp;page_count=" . $page_current . "&amp;sort=software_name\">$l_swf $l_nam</a></td>\n";
    echo "  <td><a href=\"" . $_SERVER["PHP_SELF"] . "?sub=" . $sub . "&amp;page_count=" . $page_current . "&amp;sort=software_version\">$l_ver</a></td>\n";
    echo "  <td><a href=\"" . $_SERVER["PHP_SELF"] . "?sub=" . $sub . "&amp;page_count=" . $page_current . "&amp;sort=software_publisher\">$l_pub</a></td>\n";
    echo "  <td align=\"center\"><b>Google</b></td>\n";
    echo " </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo " <tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "  <td align=\"center\"><a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($myrow["software_name"]) . "&amp;version=" . url_clean($myrow["software_version"]) . "\" title=\"Show All Systems with this software\">" . $myrow["software_count"] . "</a></td>\n";
      echo "  <td><a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($myrow["software_name"]) . "\" title=\"Show All Systems with this software\">" . $myrow["software_name"] . "</a>&nbsp;&nbsp;</td>\n";
      echo "  <td>" . $myrow["software_version"] . "&nbsp;&nbsp;</td>\n";
      echo "  <td>" . $myrow["software_publisher"] . "&nbsp;&nbsp;</td>\n";
      echo "  <td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["software_name"]) . "%22&amp;btnG=Search\"><img border=0 alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" /></a></td>\n";
      echo " </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  echo " <tr>\n  <td colspan=\"4\"><br /><b>$l_to1: " . mysql_num_rows($result) . "</b><br />\n";
  echo "                                 <b>$l_to2: " . $total_current_software . "</b><br />\n";
  echo "                                 <b>$l_to3: " . $total_software . "</b></td>\n";
  } else {}
  include "include_list_buttons.php";
  echo "</tr>\n";
} else {}



if ($sub == "sw1"){
  if (isset($_GET['sort'])){ $sort = $_GET['sort']; } else { $sort = 'system_name';}
  if ($version <> "no version"){
    $sql = "SELECT software_name, software_version, software_publisher, net_ip_address, system_uuid, system_name, system_description FROM software, system where software_name = '" . $_GET["name"] . "' AND software_version = '" . $version . "' AND software_uuid = system_uuid AND software_timestamp = system_timestamp ORDER BY " . $sort;
  } else {
    $sql = "SELECT software_name, software_version, software_publisher, net_ip_address, system_uuid, system_name, system_description FROM software, system where software_name = '" . $_GET["name"] . "' AND software_uuid = system_uuid AND software_timestamp = system_timestamp ORDER BY " . $sort;
  }
  $result = mysql_query($sql);
  if ($myrow = mysql_fetch_array($result)){
    echo "<div class=\"main_each\">\n";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
    if ($version <> "no version"){
      echo " <tr>\n  <td class=\"contenthead\" colspan=\"3\">$l_lsy " . $_GET["name"] . " $l_ver " . $version . " $l_cur.<br />&nbsp;</td>\n </tr>\n";
    } else {
      echo " <tr>\n  <td class=\"contenthead\" colspan=\"3\">$l_lsy " . $_GET["name"] . " $l_cur.<br />&nbsp;</td>\n </tr>\n";
    }
    echo " <tr>";
    echo "  <td>&nbsp;&nbsp;<a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($_GET["name"]) . "&amp;version=" . url_clean($version) . "&amp;sort=net_ip_address\">$l_ipa</a></td>\n";
    echo "  <td>&nbsp;&nbsp;<a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($_GET["name"]) . "&amp;version=" . url_clean($version) . "&amp;sort=system_name\">$l_nam</a></td>\n";
    echo "  <td>&nbsp;&nbsp;<a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($_GET["name"]) . "&amp;version=" . url_clean($version) . "&amp;sort=system_description\">$l_des</a></td>";
    if ($version == "no version"){
      echo "<td>&nbsp;&nbsp;<a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($_GET["name"]) . "&amp;version=" . url_clean($version) . "&amp;sort=software_version\">$l_ver</a></td>";
    } else {}
    echo "\n </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo " <tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "  <td>&nbsp;&nbsp;" . ip_trans($myrow["net_ip_address"]) . "</td>\n";
      echo "  <td>&nbsp;&nbsp;<a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "\">" . $myrow["system_name"] . "</a></td>\n";
      echo "  <td>&nbsp;&nbsp;" . $myrow["system_description"] . "</td>\n";
      if ($version == "no version"){
        echo "  <td>&nbsp;&nbsp;" . $myrow["software_version"] . "</td>\n";
      } else {}
      echo " </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    echo " <tr>\n  <td colspan=\"4\"><br /><b>$l_to4: " . mysql_num_rows($result) . "</b><br />\n";
  } else {}
} else {}
echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
