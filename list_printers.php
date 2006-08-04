<?php 
$page = "other";
$extra = "";
include "include.php";
echo "<td valign=\"top\">\n";

if ($sort == "system_name") {$sort = 'other_network_name';} else {}
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

$sql = "SELECT count(other_description) as count FROM other WHERE other_type = 'printer'";
$result = mysql_query($sql, $db);
$myrow = mysql_fetch_array($result);
$count = $myrow["count"];

$sql = "SELECT * FROM other WHERE other_type = 'printer' ORDER by $sort LIMIT " . $page_count . "," . $count_system;
?>

  <div class="main_each">
  <? include "include_list_buttons_css.php"; ?>
  <span class="contenthead">List All Printers.</span><br /><br />
<?
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"content\" width=\"100%\">\n";
  $result = mysql_query($sql, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo "<tr>";
    echo "  <td><a href=\"list_printers.php?sort=other_network_name\"><b>System</b></a></td>\n";
    echo "  <td><a href=\"list_printers.php?sort=other_description\"><b>Caption</b></a></td>\n";
    echo "  <td><a href=\"list_printers.php?sort=other_p_port_name\"><b>Port</b></a></td>\n";
    echo "  <td><a href=\"list_printers.php?sort=other_location\"><b>Location</b></a></td>\n";
    echo "</tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo "<tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "  <td>";
      if ($myrow["other_linked_pc"]) { echo "<a href=\"system_summary.php?pc=" . $myrow["other_linked_pc"] . "\">";}
      echo $myrow["other_network_name"];
      if ($myrow["other_id"]) { echo "</a>";}
      echo "</td>\n";
      echo "  <td><a href=\"other_summary.php?other=" . $myrow["other_id"] . "\">" . $myrow["other_description"] . "</a></td>\n";
      echo "  <td>" . $myrow["other_p_port_name"] . "</td>\n";
      echo "  <td>" . $myrow["other_location"] . "</td>\n";
      echo "</tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  echo "<tr><td colspan=3><br /><b>Total Printers: " . $count . "</b></td>\n";
  } else {
    echo "<tr><td><br />No Printers in database.</td></tr>";
  }
echo "</table>";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
