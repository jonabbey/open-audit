<?php 
$page = "software";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr>\n";
echo "<td align=\"left\" class=\"contenthead\">$l_cut - " . $name . "<br />&nbsp;</td>\n";
echo "</tr>\n";
echo "</table>\n";


  $SQL = "SELECT * FROM software WHERE software_uuid = '$pc' AND software_timestamp = '$timestamp' AND software_name LIKE 'Codec%' ORDER BY software_name";
  $result = mysql_query($SQL, $db);
  if (($myrow = mysql_fetch_array($result))){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
    echo "<tr><td class=\"menuhead\" colspan=\"3\"><br /><img src=\"images/audio_l.png\" width=\"48\" height=\"48\" alt=\"\" /> Installed Non-Microsoft Codecs</td></tr>\n";
    $bgcolor = "#F1F1F1";
    echo "<tr><td>$l_swf</td><td>$l_ver</td><td align=\"center\">$l_pub</td><td align=\"center\">$l_goo</td></tr>\n";
    do {
      echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "<td><a href=\"list_software.php?sub=sw1&amp;name=" . url_clean($myrow["software_name"]) . "\">" . $myrow["software_name"] . "</a></td>\n";
      echo "<td>" . $myrow["software_version"] . "</td>\n";
      echo "<td align=\"center\">";
      if ($myrow["software_url"]) {
        echo "<a href=\"" . $myrow["software_url"] . "\">";
      } else {}
      echo "<img src=\"images/software.png\" border=\"0\" title=\"" . $myrow["software_publisher"] . "\" /></td>\n";
      echo "<td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=" . str_replace("-","",url_clean($myrow["software_name"])) . "&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>\n";
      echo "</tr>";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {}


echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php";
?>
