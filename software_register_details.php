<?php
$page = "";
$extra = "";
$software = "";
$count = 0;
include "include.php";

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";

if (isset($_GET['id'])){$id = $_GET['id'];}else{header("Location: software_register.php");}

$sql = "SELECT software_reg_id, software_title, count(software.software_name) AS number_used, software_comments FROM ";
$sql .= "software_register, software, system WHERE ";
$sql .= "software_title = software_name AND ";
$sql .= "software_uuid = system_uuid AND ";
$sql .= "software_timestamp = system_timestamp AND ";
$sql .= "software_reg_id = '$id' ";
$sql .= "GROUP BY software_title";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
    $name = $myrow["software_title"];
    
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
    echo "<tr><td class=\"contenthead\" colspan=\"2\">Software License Register Details for: </td></tr>\n";
    echo "<tr><td>" . $myrow["software_title"] . "</td></tr>\n";
    echo "<tr><td class=\"contenthead\"><br />Usage Details.</td></tr>\n";
    echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
    echo "<td width=\"25%\"><b>Package Name&nbsp;&nbsp;</b></td>\n";
    echo "<td width=\"25%\" align=\"center\"><b>&nbsp;&nbsp;Purchased&nbsp;&nbsp;</b></td>\n";
    echo "<td width=\"25%\" align=\"center\"><b>&nbsp;&nbsp;Used&nbsp;&nbsp;</b></td>\n";
    echo "<td width=\"25%\" align=\"center\"><b>&nbsp;&nbsp;Audit&nbsp;&nbsp;</b></td>\n";
    echo "</tr>";
    do {
    
      $sql2  = "SELECT sum(license_purchase_number) as number_purchased FROM ";
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

      echo "<tr bgcolor=\"#F1F1F1\">\n";
      echo "<td>" . $myrow["software_title"] . "&nbsp;&nbsp;</td>\n";
      echo "<td align=\"center\">" . $number_purchased . "</td>\n";
      echo "<td align=\"center\">" . $number_used . "</td>\n";
    if ($number_purchased == -1) {
      echo "<td align=\"center\"></td>";
    } else {
      echo "<td align=\"center\">" . $font . $number_audit . "</font></td>";
    }
//      echo "<td align=\"center\">" . $font . $number_audit . "</font></td>\n";
      echo "</tr>\n";
      echo "</table>\n";
      echo "<table bgcolor=\"" . $bgcolor . "\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\" class=\"content\">\n";
      if ($myrow["software_comments"] <> ""){
        echo "<tr>\n";
        echo "<td colspan=\"3\"><br />Comments:<br />" . $myrow["software_comments"] . "</td>";
        echo "</tr>\n";
      } else {}
      echo "<tr>\n";
      echo "<td colspan=\"3\"><br /><a href=\"software_register_edit_comments.php?id=" . $_GET["id"] . "\">Edit Comments</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"software_add_license.php?id=" . $_GET["id"] . "\">Add License</a></td>\n";
      echo "</tr>\n";
      echo "</table>\n";
    } while ($myrow = mysql_fetch_array($result));
} else {}

if ($bgcolor == "") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }

$sql = "SELECT * FROM software_licenses WHERE license_comments <> 'OA initial license' AND license_software_id = '" . $_GET["id"] . "'";
$result = mysql_query($sql, $db);
if ($myrow = mysql_fetch_array($result)){
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo "<tr>\n";
  echo "  <td colspan=\"7\" class=\"contenthead\">Software Licenses Purchased.</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "  <td width=\"10%\" align=\"center\"><b>Purchase Date</b></td>\n";
  echo "  <td width=\"10%\" align=\"center\"><b>Number Purchased</b></td>\n";
  echo "  <td width=\"10%\" align=\"center\"><b>Vendor</b></td>\n";
  echo "  <td width=\"10%\" align=\"center\"><b>Cost Each</b></td>\n";
  echo "  <td width=\"10%\" align=\"center\"><b>License Type</b></td>\n";
  echo "  <td width=\"10%\">&nbsp;</td>\n";
  echo "  <td width=\"40%\">Comments</td>\n";
  echo "</tr>\n";
  $bgcolor = "#F1F1F1";
  do {
    echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
    echo "  <td align=\"center\">" . substr($myrow["license_purchase_date"],0,10) . "</td>\n";
    echo "  <td align=\"center\">" . $myrow["license_purchase_number"] . "</td>\n";
    echo "  <td align=\"center\">" . $myrow["license_purchase_vendor"] . "</td>\n";
    echo "  <td align=\"center\">" . $myrow["license_purchase_cost_each"] . "</td>\n";
    echo "  <td align=\"center\">" . $myrow["license_purchase_type"] . "</td>\n";
    echo "  <td align=\"center\"><a href=\"software_register_del_license.php?id=" . $myrow["license_id"] . "&amp;id2=" . $_GET["id"] . "\" onclick=\"return confirm('Do you really want to DELETE this license ?','software_register_del_license.php?id=" . $myrow["license_id"] . "&amp;id2=" . $_GET["id"] . "')\">Delete</a></td>\n";
    echo "  <td>" . $myrow["license_comments"] . "</td>\n";
    echo "</tr>\n";
    if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
  } while ($myrow = mysql_fetch_array($result));
  echo "</table>";
} else {}

      $bgcolor = "#F1F1F1";
      $sql = "select sys.system_uuid, sys.system_os_name, sys.system_description, sys.net_ip_address, sys.system_name, sw.software_name  from software sw, system sys where sw.software_name = '" . addslashes($name) . "' AND sw.software_uuid = sys.system_uuid ORDER BY sys.system_name";
      $result = mysql_query($sql, $db);
      if ($myrow = mysql_fetch_array($result)){
        echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"content\">\n";
        echo "<tr><td class=\"contenthead\" colspan=\"3\"><br />Systems with \"" . $name . "\" installed.</td></tr>\n";
        echo "<tr><td>&nbsp;&nbsp;<b>IP Address</b></td><td>&nbsp;&nbsp;<b>Name</b></td><td>&nbsp;&nbsp;<b>Description</b></td></tr>\n";
        do {
          echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
          echo "  <td width=\"33%\">&nbsp;&nbsp;" . $myrow["net_ip_address"] . "&nbsp;&nbsp;</td>\n";
          echo "  <td width=\"33%\">&nbsp;&nbsp;<a href=\"system.php?pc=" . $myrow["system_uuid"] . "&amp;view=summary\" class=\"content\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;</td>\n";
          echo "  <td width=\"33%\">&nbsp;&nbsp;" . $myrow["system_description"] . "</td>\n";
          echo "</tr>\n";

      if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
    } while ($myrow = mysql_fetch_array($result));
  } else {
    echo "No Systems have this software installed.";
  }
echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
?>
