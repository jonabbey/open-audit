<?php 
$page = "us";
include "include.php"; 

echo "<td valign=\"top\">\n";

echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
echo "<tr>";
echo "<td align=\"left\" class=\"contenthead\">$l_uag - " . $name . "<br />&nbsp;</td>";
echo "</tr>";
echo "</table>";

if (($sub == "us") or ($sub == "all")){
  $bgcolor = "#FFFFFF";
  $SQL = "SELECT * FROM users WHERE users_uuid = '$pc' AND users_timestamp = '$timestamp' ORDER BY users_name";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
    echo "<tr><td class=\"contenthead\"><br /><img src=\"images/users_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_usr</td></tr>\n";
    do {
      $SQL2 = "SELECT * FROM users_detail WHERE ud_name = '" . $myrow["users_name"] . "'";
      $result2 = mysql_query($SQL2, $db);
      $myrow2 = mysql_fetch_array($result2);
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_usn:&nbsp;</td><td>" . $myrow["users_name"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_ful:&nbsp;</td><td>" . $myrow["users_full_name"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_usd:&nbsp;</td><td>" . $myrow["users_sid"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_dis:&nbsp;</td><td>" . $myrow["users_disabled"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_psc:&nbsp;</td><td>" . $myrow["users_password_changeable"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_psr:&nbsp;</td><td>" . $myrow["users_password_required"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_des:&nbsp;</td><td>" . $myrow2["ud_description"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td colspan=\"2\"><br /></td></tr>";
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {
    echo "<p class=\"menuhead\">&nbsp;&nbsp;$l_nou.</p>"; 
  }
} else {}

echo "<br /><br />";

if (($sub == "gr") or ($sub == "all")){
  $bgcolor = "#FFFFFF";
  $SQL = "SELECT * FROM groups WHERE groups_uuid = '$pc' AND groups_timestamp = '$timestamp' ORDER BY groups_name";
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
    echo "<tr><td class=\"contenthead\"><br /><img src=\"images/groups_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_grp</td></tr>\n";
    do {
      $SQL2 = "SELECT * FROM groups_details WHERE gd_name = '" . $myrow["groups_name"] . "'";
      $result2 = mysql_query($SQL2, $db);
      $myrow2 = mysql_fetch_array($result2);
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td width=\"150\">$l_grn:&nbsp;</td><td>" . $myrow["groups_name"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_mbr:&nbsp;</td><td>" . $myrow["groups_members"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td valign=\"top\">$l_des:&nbsp;</td><td>" . $myrow2["gd_description"] . "</td></tr>";
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td colspan=\"2\"><br /></td></tr>";
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
  } else {
    echo "<p class=\"section_header_3\">$l_nog.</p>"; 
  }
} else {}

echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";

include "include_png_replace.php";
?>
