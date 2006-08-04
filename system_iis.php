<?php 
$page = "iis";
include "include.php"; 
echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" >";
echo "<tr>";
echo "<td align=\"left\" class=\"contenthead\">$l_iis - " . $name . "<br />&nbsp;</td>";
echo "</tr>";
echo "</table>";

$opt_count = 0;
$SQL = "SELECT * FROM iis WHERE iis_uuid = '$pc' AND iis_timestamp = '$timestamp'";
$result = mysql_query($SQL, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" >\n";
  do {
    echo "<tr><td class=\"menuhead\" colspan=\"2\"><img src=\"images/browser_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_iit:&nbsp;" . $myrow["iis_site"] . "</td></tr>\n";
    echo "<tr><td>$l_des:&nbsp;</td><td>" . $myrow["iis_description"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_hmd:&nbsp;</td><td>" . $myrow["iis_home_directory"] . "</td></tr>\n";
    echo "<tr><td>$l_dir:&nbsp;</td><td>" . $myrow["iis_directory_browsing"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_ddc:&nbsp;</td><td>" . $myrow["iis_default_documents"] . "</td></tr>\n";
    echo "<tr><td>$l_lge:&nbsp;</td><td>" . $myrow["iis_logging_enabled"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_lof:&nbsp;</td><td>" . $myrow["iis_logging_format"] . "</td></tr>\n";
    echo "<tr><td>$l_lop:&nbsp;</td><td>" . $myrow["iis_logging_time_period"] . "</td></tr>\n";
    echo "<tr bgcolor=\"$bg1\"><td>$l_lod:&nbsp;</td><td>" . $myrow["iis_logging_dir"] . "</td></tr>\n";
    echo "<tr><td>$l_seb / $l_por:&nbsp;</td><td>" . htmlspecialchars($myrow["iis_secure_ip"]) . " / " . htmlspecialchars($myrow["iis_secure_port"]) . "</td></tr>\n";
    $SQL2 = "SELECT * from iis_ip where iis_ip_uuid = '$pc' AND iis_ip_timestamp = '$timestamp' AND iis_ip_site = '" . $myrow["iis_site"] . "' ORDER BY iis_ip_site";
    $result2 = mysql_query($SQL2, $db);
    if ($myrow2 = mysql_fetch_array($result2)){
      do {
        echo "<tr bgcolor=\"$bg1\"><td>$l_ipa / $l_por / $l_hos:&nbsp;</td><td colspan=\"2\">" . htmlspecialchars($myrow2["iis_ip_ip_address"]) . " / " . $myrow2["iis_ip_port"] . " / " . htmlspecialchars($myrow2["iis_ip_host_header"]) . "</td></tr>\n";
      } while ($myrow2 = mysql_fetch_array($result2));
    } else {}
    $SQL2 = "SELECT * from iis_vd where iis_vd_uuid = '$pc' AND iis_vd_timestamp = '$timestamp' AND iis_vd_site = '" . $myrow["iis_site"] . "' ORDER BY iis_vd_name";
    $result2 = mysql_query($SQL2, $db);
    $bg3 = $bg1;
    if ($myrow2 = mysql_fetch_array($result2)){
      do {
        if ($bg3 == $bg2) {$bg3 = $bg1;} else {$bg3 = $bg2;}
        echo "<tr><td>$l_vir $l_nam: </td><td>" . $myrow2["iis_vd_name"] . "</td></tr>\n";
        echo "<tr bgcolor=\"$bg1\"><td>$l_vir $l_paa: </td><td>" . $myrow2["iis_vd_path"] . "</td></tr>\n";
      } while ($myrow2 = mysql_fetch_array($result2));
    } else {}
  } while ($myrow = mysql_fetch_array($result));
  echo "</table>";
} else {
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
  echo "<tr>";
  echo "<td class=\"contenthead\"><img src=\"images/browser_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_iiu.</td>\n"; 
  echo "</tr>";
  echo "</table>";
}
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
