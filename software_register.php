<?php
$page = "";
$extra = "";
$software = "";
$count = -1;
if (isset($_GET['software'])) {$software = $_GET['software'];} else {}
if (isset($_GET['sort'])) {$sort = $_GET['sort'];} else {$sort= "system_name";}
include "include.php";

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
#$sql =  "SELECT software_reg_id, software_title, count(software.software_name) AS number_used, sum(license_purchase_number) as number_purchased FROM ";
#$sql .= "software_register, software, system, software_licenses WHERE ";
#$sql .= "software_reg_id = license_software_id AND ";
#$sql .= "software_title = software_name AND ";
#$sql .= "software_uuid = system_uuid AND ";
#$sql .= "software_timestamp = system_timestamp ";
#$sql .= "GROUP BY software_title";

$sql = "SELECT software_reg_id, software_title, count(software.software_name) AS number_used FROM ";
$sql .= "software_register, software, system WHERE ";
$sql .= "software_title = software_name AND ";
$sql .= "software_uuid = system_uuid AND ";
$sql .= "software_timestamp = system_timestamp ";
$sql .= "GROUP BY software_title";

$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
  echo "<tr>\n";
  echo "  <td class=\"contenthead\">Software License Register.<br />&nbsp;</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td width=\"25%\"><b>Package&nbsp;&nbsp;</b></td>\n";
  echo "<td align=\"center\" width=\"25%\"><b>&nbsp;&nbsp;Purchased&nbsp;&nbsp;</b></td>\n";
  echo "<td align=\"center\" width=\"25%\"><b>&nbsp;&nbsp;Used&nbsp;&nbsp;</b></td>\n";
  echo "<td align=\"center\" width=\"25%\"><b>&nbsp;&nbsp;Audit&nbsp;&nbsp;</b></td>\n";
  echo "</tr>\n";
  do {    $sql2  = "SELECT sum(license_purchase_number) as number_purchased FROM ";
    $sql2 .= "software_licenses, software_register WHERE ";
    $sql2 .= "license_software_id = software_reg_id AND ";
    $sql2 .= "software_title = '" . $myrow['software_title'] . "'";
    $result2 = mysql_query($sql2, $db);
    $myrow2 = mysql_fetch_array($result2);
    
    
    $number_purchased = $myrow2["number_purchased"];
    $number_used = $myrow["number_used"];
    settype($number_purchased, "integer");
    settype($number_used, "integer");
    $number_audit = $number_purchased - $number_used;
    $font = "<font>";
    if ($number_audit < "0") { $font = "<font color=\"red\">";}
    if ($number_audit == "0") { $font = "<font color=\"blue\">";}
    if ($number_audit > "0") { $font = "<font color=\"green\">";}
      
    $count = $count + 1;
    if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
    echo "<tr bgcolor=\"" . $bgcolor . "\">";
    echo "<td><a href=\"software_register_details.php?id=" . $myrow["software_reg_id"] . "\">" . $myrow["software_title"] . "</a>&nbsp;&nbsp;</td>";
    if ($number_purchased == -1) {
      echo "<td align=\"center\">Free</td>";
    } else {
      echo "<td align=\"center\">" . $number_purchased . "</td>";
    }
    echo "<td align=\"center\">" . $number_used . "</td>";
    if ($number_purchased == -1) {
      echo "<td align=\"center\"></td>";
    } else {
      echo "<td align=\"center\">" . $font . $number_audit . "</font></td>";
    }
    echo "</tr>";
  } while ($myrow = mysql_fetch_array($result));
  echo "</table>";
} else {
  echo "<p class=\"content\">No Packages in database.</p>"; 
}


echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
?>
