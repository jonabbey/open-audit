<?php
set_time_limit(300); 
$page = "database_restore_sql";
include "include.php";
echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">\n";

//$backup_name = $_GET['backup_name'];
//$filename = "backup/".$backup_name;
  

if(!(isset($_POST['submit']))){
$backup_name = $_GET['backup_name'];

  

  //echo __("If you are not sure how to complete this tasks, please check the")." <a href=\"http://www.open-audit.org/phpbb2/\">".__("Forums")."</a> ".__("in the FAQ section").".<br />\n";
  //echo "</p>\n";
  echo "        <form name=\"setup\" action=\"database_restore_sql.php\" method=\"post\" >\n";
  echo "        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo "          <tr>\n";
  echo "            <td class=\"contenthead\" colspan=\"2\">".__("Restore Database")."</td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "            <td colspan=\"2\"><hr /></td>\n";
  echo "          </tr>\n";
  echo "          <tr>\n";
  echo "    </select></td>\n";
  echo "            <td>".__("Restoring backup $backup_name")."</td></tr>\n";
  echo "            <td>".__("What database name do you want to restore to")." ?</td>\n";
  echo "            <td><input type=\"text\" size=\"20\" name=\"mysql_data\" value=\"openaudit\" class=\"for_forms\" /></td>\n";
  echo "          </tr>\n";
  echo "            <td>".__("Click Restore to continue")."</td>\n";
  echo "            <td><input type=\"submit\" name=\"submit\" value=\"".__("Restore")."\" /></td>\n";
    echo "            <td><input type=\"hidden\" name=\"backup_name\" value=\"$backup_name\" /></td>\n"; 
  echo "          </tr>\n";
  echo "        </table>\n";
  echo "        </form>\n";

} else {

$backup_name = $_POST['backup_name'];
$filename = ".\\backup\\".$backup_name.".sql";

  echo "        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
  echo "          <tr>\n";

  echo "<tr><td>".__("Opening MySQL Dump file and reading contents").".</td>\n";

  $handle = fopen($filename, "rb");
  $contents = fread($handle, filesize($filename));
  fclose($handle);
  echo "<td>".__("Done").".</td><td><img src=\"images/button_ok.png\" width=\"16\" height=\"16\" /></td></tr>\n";
  //echo "<tr><td>".__("Creating database").".</td>\n";
   $sql = $contents;
  $sql2 = explode(";", $sql);
  echo "<tr><td>".__("Running SQL upload").".</td>";
  foreach ($sql2 as $sql3) {
  //echo "<tr><td>" . $sql3 . "</td></tr>";
  $result = mysql_query($sql3 . ";") ;
    if (!$result) {
        echo "</tr><tr><td><font color=\"red\">" . $sql3 . "</font></td></tr><tr><td colspan=\"2\"><h3>MySQL Error:</h3> " . mysql_error() . "</td></tr><tr>\n";
        }
  }
  echo "<td>".__("Done").".</td></tr>\n";
  echo "<tr><td><br />".__("Click")." <a href=\"index.php\">".__("here")."</a> ".__("to continue").".</td></tr>\n";
  echo "</table>\n";
}

echo "</div>";
echo "</td>\n";
include "include_right_column.php";
echo "</body>";
echo "</html> ";
include "include_png_replace.php"
?>