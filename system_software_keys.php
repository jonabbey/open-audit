<?php 
$page = "software";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
echo "<tr>";
echo "<td align=\"left\" class=\"contenthead\">$l_cdm -  " . $name . "<br />&nbsp;</td>";
echo "</tr>";
echo "</table>";
  

    $sql = "SELECT * FROM ms_keys WHERE ms_keys_uuid = '$pc' AND ms_keys_timestamp = '$timestamp' AND ms_keys_key_type LIKE 'windows%'";
    $result = mysql_query($sql, $db);
    if (($myrow = mysql_fetch_array($result))){
    $bgcolor = "#F1F1F1";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
    echo "<tr><td class=\"menuhead\" colspan=\"2\"><img src=\"images/key_2_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_cdw</td></tr>";
    do {
    echo "<tr bgcolor=\"" . $bgcolor . "\">";
    echo "<td><br />$l_swf:&nbsp;<br />$l_ink:&nbsp;<br />&nbsp;</td>";
    echo "<td><br />" . $myrow["ms_keys_name"] . "<br />" . $myrow["ms_keys_cd_key"] . "&nbsp;<br />&nbsp;</td></tr>";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
    } else {}
  

    $sql = "SELECT * FROM ms_keys WHERE ms_keys_uuid = '$pc' AND ms_keys_timestamp = '$timestamp' AND ms_keys_key_type = 'office_2003'";
    $result = mysql_query($sql, $db);
    if (($myrow = mysql_fetch_array($result))){
    $bgcolor = "#F1F1F1";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
    echo "<tr><td class=\"menuhead\" colspan=\"2\"><img src=\"images/key_2_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_cdo</td></tr>";
    do {
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>&nbsp;</td><td>&nbsp;</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_swf: </td><td>" . $myrow["ms_keys_name"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_rel: </td><td>" . $myrow["ms_keys_release"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_edt: </td><td>" . $myrow["ms_keys_edition"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_ink: </td><td>" . $myrow["ms_keys_cd_key"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>&nbsp;</td><td>&nbsp;</td></tr>";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
    } else {}


    $sql = "SELECT * FROM ms_keys WHERE ms_keys_uuid = '$pc' AND ms_keys_timestamp = '$timestamp' AND ms_keys_key_type = 'office_xp'";
    $result = mysql_query($sql, $db);
    if (($myrow = mysql_fetch_array($result))){
    $bgcolor = "#F1F1F1";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
    echo "<tr><td class=\"menuhead\" colspan=\"2\"><img src=\"images/key_2_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_cdx</td></tr>";
    do {
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>&nbsp;</td><td>&nbsp;</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_swf: </td><td>" . $myrow["ms_keys_name"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_rel: </td><td>" . $myrow["ms_keys_release"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_edt: </td><td>" . $myrow["ms_keys_edition"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_ink: </td><td>" . $myrow["ms_keys_cd_key"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>&nbsp;</td><td>&nbsp;</td></tr>";
    $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
    } while ($myrow = mysql_fetch_array($result));
    echo "</table>";
    } else {}


    $sql = "SELECT * FROM ms_keys WHERE ms_keys_uuid = '$pc' AND ms_keys_timestamp = '$timestamp' AND ms_keys_key_type NOT LIKE 'office%' AND ms_keys_key_type NOT LIKE 'windows%'";
    $result = mysql_query($sql, $db);
    if (($myrow = mysql_fetch_array($result))){
    $bgcolor = "#F1F1F1";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
    echo "<tr><td class=\"menuhead\" colspan=\"2\"><img src=\"images/key_2_l.png\" width=\"48\" height=\"48\" alt=\"\" /> $l_cdt</td></tr>";
    do {
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>&nbsp;</td><td>&nbsp;</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_swf: </td><td>" . $myrow["ms_keys_name"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_rel: </td><td>" . $myrow["ms_keys_release"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_edt: </td><td>" . $myrow["ms_keys_edition"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$l_ink: </td><td>" . $myrow["ms_keys_cd_key"] . "</td></tr>";
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>&nbsp;</td><td>&nbsp;</td></tr>";
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
