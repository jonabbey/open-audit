<?php 
$page = "software";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">\n";
echo "<tr>\n";
echo "<td align=\"left\" class=\"contenthead\">$l_cut " . $name . "<br />&nbsp;</td>\n";
echo "</tr>\n";
echo "</table>\n";

if (($sub == "is") or ($sub == "all")){
  $SQL = "SELECT * FROM software WHERE software_uuid = '$pc' AND software_timestamp = '$timestamp' AND software_name NOT LIKE '%codec%' AND software_name NOT LIKE '%hotfix%' AND software_name NOT LIKE '%update%' AND software_name NOT LIKE '%Service Pack%' AND software_system_component <> '1' ORDER BY software_name";
//echo $SQL . "<br />";
  $SQL = "SELECT software_name, software_version, software_url, software_publisher FROM software, system WHERE software_uuid = '$pc' AND software_uuid = system_uuid AND software_timestamp = system_timestamp AND software_name NOT LIKE '%codec%' AND software_name NOT LIKE '%hotfix%' AND software_name NOT LIKE '%update%' AND software_name NOT LIKE '%Service Pack%' AND software_system_component <> '1' ORDER BY software_name";
//echo "&nbsp;<br />" . $SQL . "<br />";
  $result = mysql_query($SQL, $db);
  if (($myrow = mysql_fetch_array($result))){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">\n";
    echo "<tr><td class=\"contenthead\"><br /><img src=\"images/software_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_ins</td></tr>\n";
    $bgcolor = "#F1F1F1";
    echo "<tr><td>$l_app</td><td>$l_ver</td><td>$l_pub</td><td align=\"center\">$l_goo</td></tr>\n";
    do {
      echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "<td><a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($myrow["software_name"]) . "\">" . $myrow["software_name"] . "</a></td>\n";
      echo "<td>" . $myrow["software_version"] . "</td>\n";
      echo "<td>";
      if ($myrow["software_url"]) {
        echo "<a href=\"" . $myrow["software_url"] . "\">" . $myrow["software_publisher"] . "</a></td>\n"; 
      } else {
        echo $myrow["software_publisher"] . "</td>\n";
      } 
      echo "<td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["software_name"]) . "%22&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>\n";
      echo "</tr>";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
} else {}

if (($sub == "sy") or ($sub == "all")){
  $SQL = "SELECT * FROM software WHERE software_uuid = '$pc' AND software_timestamp = '$timestamp' AND software_name NOT LIKE '%hotfix%' AND software_system_component <> '' ORDER BY software_name";
  $result = mysql_query($SQL, $db);
  if (($myrow = mysql_fetch_array($result))){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">\n";
    echo "<tr><td class=\"contenthead\"><br /><img src=\"images/settings_2_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_int</td></tr>\n";
    $bgcolor = "#F1F1F1";
    echo "<tr><td>$l_app</td><td>$l_ver</td><td>$l_pub</td><td align=\"center\">$l_goo</td></tr>\n";
    do {
      echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "<td><a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($myrow["software_name"]) . "\">" . $myrow["software_name"] . "</a></td>\n";
      echo "<td>" . $myrow["software_version"] . "</td>\n";
      echo "<td>" . $myrow["software_publisher"] . "</td>\n";
      echo "<td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["software_name"]) . "%22&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>\n";
      echo "</tr>";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
} else {}

if (($sub == "ph") or ($sub == "all")){
  $opt_count = 0;
  $SQL = "SELECT * FROM software WHERE software_uuid = '$pc' AND software_timestamp = '$timestamp' AND (software_name LIKE '%hotfix%' OR software_name LIKE '%update%' OR software_name LIKE '%Service Pack%') AND software_system_component <> '1' ORDER BY software_name";
  $result = mysql_query($SQL, $db);
  $bgcolor = "#F1F1F1";
  if ($myrow = mysql_fetch_array($result)){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" >\n";
    echo "<tr><td class=\"contenthead\" colspan=\"3\"><br /><img src=\"images/software_2_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_upd</td></tr>";
    echo "<tr><td>$l_nam</td><td>$l_ver</td><td>$l_pub</td><td align=\"center\">$l_goo</td></tr>\n";
    do {
      echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "<td><a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($myrow["software_name"]) . "\">" . $myrow["software_name"] . "</a></td>\n";
      echo "<td>" . $myrow["software_version"] . "</td>\n";
      echo "<td>";
      if ($myrow["software_url"]) {
        echo "<a href=\"" . url_clean($myrow["software_url"]) . "\">" . $myrow["software_publisher"] . "</a></td>\n"; 
      } else {
        echo $myrow["software_publisher"] . "</td>\n";
      } 
      echo "<td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["software_name"]) . "%22&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>\n";
      echo "</tr>";
        $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
} else {}

if (($sub == "rs") or ($sub == "all")){
  $sql = "SELECT * FROM startup WHERE startup_uuid = '$pc' AND startup_timestamp = '$timestamp' ORDER BY startup_location, startup_caption";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  width=\"100%\">\n";
    echo "<tr><td class=\"contenthead\" colspan=\"3\"><br /><img src=\"images/scsi_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_ras</td></tr>\n";
    echo "<tr><td>Name</td><td>User</td><td>Location</td><td align=\"center\">Executable</td><td align=\"center\">Google</td></tr>";
    $bgcolor = "#FFFFFF";
    do {
      if (substr($myrow["startup_location"],0,2) == "HK"){$location = "Registry";}else{$location = NULL;}
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "  <td><a href=\"list_software_startup.php?name=" . url_clean($myrow["startup_caption"]) . "\">" . $myrow["startup_caption"] . "</a></td>\n";
      echo "  <td>" . $myrow["startup_user"] . "</td>\n";
      if (isset($location)){echo "  <td><a title=\"" . $myrow["startup_location"] . "\">$location</a></td>\n";}else{echo "  <td>" . $myrow["startup_location"] . "</td>\n";}
      echo "  <td align=\"center\"><img src=\"images/software.png\" border=\"0\" title=\"" . $myrow["startup_command"] . "\" alt=\"" . $myrow["startup_command"] . "\" /></td>\n";
      echo "  <td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["startup_caption"]) . "%22&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>\n";
      echo "</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}
} else {}

echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php";
?>
