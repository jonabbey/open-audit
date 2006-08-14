<?php 
$page = "";
$extra = "";
include "include.php";
echo "<td valign=\"top\">\n";

if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

if ($sub <> "sw1"){
  if (isset($_GET['sort'])){ $sort = $_GET['sort']; } else { $sort = 'bho_program_file';}
  $SQL = "SELECT DISTINCT bho_program_file from browser_helper_objects WHERE bho_status = 'Installed'";
  $result = mysql_query($SQL, $db);
  $total_bho_installed = mysql_num_rows($result);

  $SQL = "SELECT count(bho_program_file) as bho_count, bho_program_file, bho_status, bho_code_base from browser_helper_objects group by bho_program_file, bho_status ORDER BY $sort LIMIT " . $page_count . "," . $count_system;
  $SQL_count = "SELECT DISTINCT bho_program_file from browser_helper_objects";
  ?>
  <div class="main_each">
  <?php include "include_list_buttons_css.php"; ?>
  <span class="contenthead"><?php echo $l_lib; ?></span><br /><br />
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
  <?php
  $result = mysql_query($SQL, $db);
  if ($myrow = mysql_fetch_array($result)){
    echo " <tr>\n";
    echo "  <td align=\"center\"><a href=\"" . $_SERVER["PHP_SELF"] . "?sort=bho_count&amp;page_count=" . $page_current . "\">$l_cnt</td>\n";
    echo "  <td><a href=\"" . $_SERVER["PHP_SELF"] . "?sort=bho_program_file&amp;page_count=" . $page_current . "\">$l_nam</a></td>\n";
    echo "  <td><a href=\"" . $_SERVER["PHP_SELF"] . "?sort=bho_status&amp;page_count=" . $page_current . "\">$l_sts</a></td>\n";
    echo "  <td><a href=\"" . $_SERVER["PHP_SELF"] . "?sort=bho_code_base&amp;page_count=" . $page_current . "\">$l_cob</a></td>\n";
    echo "  <td align=\"center\">Google</td>\n";
    echo " </tr>\n";
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo " <tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "  <td align=\"center\"><a href=\"list_software_bho.php?sub=sw1&amp;name=" . url_clean($myrow["bho_program_file"]) . "\" title=\"Show All Systems with this software\">" . $myrow["bho_count"] . "</a></td>\n";
      echo "  <td>" . $myrow["bho_program_file"] . "</td>\n";
      echo "  <td>" . $myrow["bho_status"] . "</td>\n";
      echo "  <td><a title=\"" . $myrow["bho_code_base"] . "\">$l_cob</a></td>\n";
      echo "  <td align=\"center\"><a href=\"http://www.google.com/search?num=30&amp;hl=en&amp;lr=lang_en&amp;ie=UTF-8&amp;oe=UTF-8&amp;safe=off&amp;q=%22" . url_clean($myrow["bho_program_file"]) . "%22&amp;btnG=Search\"><img border=0 alt=\"Google Search\" title=\"Google Search\" src=\"images/button_google.gif\" /></a></td>\n";
      echo " </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  $total = mysql_query($SQL_count, $db);
  $num_rows = mysql_num_rows($total);
  echo " <tr>\n  <td colspan=\"4\"><br /><b>$l_to1: " . mysql_num_rows($result) . "</b><br />\n";
  echo "                                 <b>$l_to5: " . $total_bho_installed . "</b><br />\n";
  echo "                                 <b>$l_to3: " . $num_rows . "</b></td>\n";
  } else {}
  echo " <tr>\n";
  include "include_list_buttons.php";
  echo " </tr>\n";
} else {}

if ($sub == "sw1"){
  $sql = "select sys.system_uuid, sys.system_description, sys.net_ip_address, sys.system_name, bho.bho_program_file from browser_helper_objects bho, system sys where bho.bho_program_file = '" . mysql_escape_string($_GET["name"]) . "' and bho.bho_uuid = sys.system_uuid AND bho.bho_timestamp = sys.system_timestamp ORDER BY sys.system_name";
  $result = mysql_query($sql, $db);
  echo "<div class=\"main_each\">\n";
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo " <tr>\n  <td class=\"contenthead\" colspan=\"3\">List Systems with <i>\"" . $_GET["name"] . "\"</i> installed.<br />&nbsp;</td>\n </tr>\n";
  echo " <tr>";
  echo "  <td width=\"100\">&nbsp;&nbsp;<b>IP Address</b></td>\n";
  echo "  <td width=\"100\">&nbsp;&nbsp;<b>Name</b></td>\n";
  echo "  <td width=\"450\">&nbsp;&nbsp;<b>Description</b></td>\n </tr>\n";
  if ($myrow = mysql_fetch_array($result)){
    do {
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
      echo " <tr bgcolor=\"" . $bgcolor . "\">\n";
      echo "  <td>&nbsp;&nbsp;" . ip_trans($myrow["net_ip_address"]) . "&nbsp;&nbsp;</td>\n";
      echo "  <td>&nbsp;&nbsp;<a href=\"system_summary.php?pc=" . $myrow["system_uuid"] . "\">" . $myrow["system_name"] . "</a>&nbsp;&nbsp;</td>\n";
      echo "  <td>&nbsp;&nbsp;" . htmlentities($myrow["system_description"]) . "</td>\n";
      echo " </tr>\n";
    } while ($myrow = mysql_fetch_array($result));
  } else {
    echo "<tr><td colspan=\"3\">No Systems have this software installed.</td></tr>";
  }
} else {}
echo "</table>\n";
echo "</div>\n";
echo "</td>\n";
include "include_right_column.php";
echo "</body>\n";
echo "</html>\n";
include "include_png_replace.php";
?>
