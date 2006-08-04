<?php 
$page = "se";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\">\n";
echo "<tr>\n";
echo "<td align=\"left\" class=\"contenthead\">$l_cut " . $name . "<br />&nbsp;</td>\n";
echo "</tr>\n";
echo "</table>\n";

    $SQL = "SELECT * FROM service WHERE service_uuid = '$pc' AND service_timestamp = '$timestamp' ORDER BY service_display_name";
    $result = mysql_query($SQL, $db);
    if ($myrow = mysql_fetch_array($result)){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
    echo "<tr><td class=\"contenthead\"><img src=\"images/services_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_ise</td></tr>\n";
    echo "</table>";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\">\n";
    do {
      $SQL2 = "SELECT * FROM service_details WHERE sd_display_name = '" . $myrow["service_display_name"] . "'";
      $result2 = mysql_query($SQL2, $db);
      $myrow2 = mysql_fetch_array($result2);
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\" width=\"100\">$l_nam:</td><td><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=windows%22" . url_clean($myrow["service_display_name"]) . "%22service&amp;btnG=Search\">" . $myrow["service_display_name"] . "</a></td></tr>\n";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">$l_str:</td><td>" . $myrow["service_start_mode"] . "</td></tr>\n";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">$l_stt:</td><td>" . $myrow["service_state"] . "</td></tr>\n";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">$l_std:</td><td>" . $myrow["service_started"] . "</td></tr>\n";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">$l_exe:</td><td>" . $myrow["service_path_name"] . "</td></tr>\n";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">$l_des:</td><td>" . $myrow2["sd_description"] . "<br />&nbsp;</td></tr>\n";
    } while ($myrow = mysql_fetch_array($result));
    ?>
    </table>
    <?php
    } else {}


echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php";
?>
