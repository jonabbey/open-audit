<?php 
$page = "graphs";
include "include.php"; 

echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
echo "<tr><td class=\"contenthead\" colspan=\"4\">Partition Usage for " . $myrow["net_ip_address"] . " - " . $myrow["system_name"] . "</td></tr>";
$disk_letter_old = "";
$sql = "SELECT * FROM graphs_disk WHERE disk_uuid = '$pc' ORDER BY disk_letter, disk_timestamp";
$result = mysql_query($sql, $db);
echo "<tr><td valign=\"top\">";
if ($myrow = mysql_fetch_array($result)){
  do {
    if ($myrow['disk_letter'] == $disk_letter_old){
    } else {
      echo "<hr /></td></tr><tr><td>Drive: " . ereg_replace (":", "", $myrow['disk_letter']) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      ";
      $sql2 = "select * from partition where partition_uuid = '$pc' and partition_caption = '" . $myrow['disk_letter'] . "'";
      $result2 = mysql_query($sql2, $db);
      $myrow2 = mysql_fetch_array($result2);
      echo "Partition Size: " . number_format($myrow2['partition_size']) . " MB&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      echo "Current Free Space: " . number_format($myrow2['partition_free_space']) . "&nbsp;MB&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      $used = number_format($myrow2['partition_size'] - $myrow2['partition_free_space']);
      echo "Current Disk Used: " . $used . " MB</td></tr>";
      echo "<tr><td><img src=\"images/graph_side.gif\" />";
    }
    $disk_time = $myrow['disk_timestamp'];
    $svDateOutput = "YYYY-MM-DD hh:mm";
    $disk_percent = $myrow['disk_percent'];
    $disk_time2 = FncChangeTimestamp ($disk_time, $svDateOutput);
    echo "<img src=\"system_graphs_image.php?disk_percent=" . $disk_percent . "\" alt=\"Partition: " . ereg_replace (":", "", $myrow['disk_letter']) . "\n";
    echo "Percentage Used: " . $disk_percent . "% \n";
    echo "Timestamp: " . $disk_time2  . "\" title=\"Partition: " . ereg_replace (":", "", $myrow['disk_letter']) . "\n";
    echo "Percentage Used: " . $disk_percent . "%\n";
    echo "Timestamp: " . $disk_time2  . "\" />";
    $disk_letter_old = $myrow['disk_letter'];
  } while ($myrow = mysql_fetch_array($result));
} else {}
echo "</td></tr>";

echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";


function FncChangeTimestamp ($svDate, $svDateOutput)
  {
    $year  = substr($svDate,0,4);
    $month = substr($svDate,5,2);
    $day   = substr($svDate,8,2);
    $hour  = substr($svDate,11,2);
    $minute= substr($svDate,14,2);
    $sec   = substr($svDate,17,2);
    $svDateOutput = ereg_replace ("YYYY", $year, $svDateOutput);
    $svDateOutput = ereg_replace ("MM", $month, $svDateOutput);
    $svDateOutput = ereg_replace ("DD", $day, $svDateOutput);
    $svDateOutput = ereg_replace ("hh", $hour, $svDateOutput);
    $svDateOutput = ereg_replace ("mm", $minute, $svDateOutput);
    $svDateOutput = ereg_replace ("ss", $sec, $svDateOutput);
    return $svDateOutput;
  }; 
?>
