<?php 
$page = "software";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
echo "<tr>";
echo "<td align=\"left\" class=\"contenthead\">$l_cut " . $name . "<br />&nbsp;</td>";
echo "</tr>";
echo "</table>";

  $sql = "SELECT * FROM browser_helper_objects WHERE bho_uuid = '$pc' AND bho_timestamp = '$timestamp' ORDER BY bho_program_file";
  $result = mysql_query($sql, $db);
  if (($myrow = mysql_fetch_array($result))){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
    echo "<tr><td class=\"menuhead\" colspan=\"4\"><img src=\"images/browser_bho_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_iec</td></tr>";
    echo "<tr><td>$l_nam</td><td>$l_sts</td><td align=\"center\">$l_goo</td><td align=\"center\">$l_hpg</td></tr>\n";
    $bgcolor = "#F1F1F1";
    do {
      echo "<tr bgcolor=\"" . $bgcolor . "\">";
      echo "  <td><a href=\"list_software_bho.php?sub=sw1&amp;name=" . url_clean($myrow["bho_program_file"]) . "\">" . $myrow["bho_program_file"] . "</a></td>\n";
      echo "  <td>" . $myrow["bho_status"] . "</td>\n";
      echo "  <td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["bho_program_file"]) . "%22&amp;btnG=Search\"><img border=\"0\" alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" width=\"16\" height=\"16\" /></a></td>\n";
      echo "  <td align=\"center\"><a href=\"" . url_clean($myrow["bho_code_base"]) . "\">Link</a></td>\n";
      echo "</tr>\n";
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {echo "<p class=\"menuhead\"><img src=\"images/browser_bho_l.png\" width=\"48\" height=\"48\" alt=\"\" />&nbsp;&nbsp;No IE BHO's installed.</p>"; }
  

echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php";
?>
