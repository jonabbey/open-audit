<?php
//    Open Audit Backup Database
//    backup to folder with current timestamp.
include "include.php";
//$this_page="https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
//echo "<meta name=\"refresh\" content=\"10;".$this_page."\">";
//
$backup_name = $_GET['backup_name'];

$add_drop_tables = "n";

if (isset($GET['add_drop_tables'])) {
    $add_drop_tables = $_GET['add_drop_tables'];
    }


//function microtime_float()
//{
//    list($usec, $sec) = explode(" ", microtime());
//    return ((float)$usec + (float)$sec);
//}
$time_start = microtime_float();

$newline = "\r\n";
$page = "database_backup_sql.php";
$bgcolor = "#FFFFFF";
// This is a bit crude... FIXME I need to find a more interactive method for this, rather than just leaving the script for 10 mins, and then timing out if no result.
set_time_limit(600);
$backup = '';

echo "<td valign=\"top\">$newline";
echo "<div class=\"main_each\">$newline";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" >$newline";
echo "  <tr><td class=\"contenthead\">".__("Backing up the Database")."</td></tr>";
echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>";
echo "<tr>";
 echo "<td class=\"views_tablehead\">".__("Tables")."</td>\n";
 echo "<td class=\"views_tablehead\">".__("Size")."</td>\n";
 echo "<td class=\"views_tablehead\">".__("Backup")."</td>\n";
echo "</tr>\n";

mysql_select_db($backup_name);

$today = date("_d-m-Y_H-i-s");
$backup_dir = './backup/';

if (!file_exists($backup_dir)) {
   mkdir($backup_dir);
}
define ('Name','Name');
define ('Auto_increment','Auto_increment');

$backup_filename = $backup_dir.$backup_name.'_Backup'.$today.'.sql';
$handle = fopen($backup_filename, "w");
$table_len=strlen($backup);

$tab_status = mysql_query("SHOW TABLE STATUS");
while($all = mysql_fetch_assoc($tab_status)):
   $tbl_stat[$all[Name]] = $all[Auto_increment];
endwhile;
unset($backup);

//$tables = mysql_list_tables($mysql_database); Depreciated... use SHOW TABLES FROM instead...

$tables = mysql_query("SHOW TABLES FROM $backup_name");

$date_time = date('l dS \of F Y h:i:s A');

$backup = '';
$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$backup .= "-- -------------------http://www.open-audit.org//-------------------$newline";
$backup .= "-- --------------------------------------------------------$newline";
$backup .= "-- ----------  $date_time   -----------$newline";
$backup .= "-- ----------  ".__("Open Audit Database Backup")."  -----------$newline";
$backup .= "-- ----Database Name --".$backup_name."----$newline";
$backup .= "-- Created by .. $url --$newline";
$backup .= "-- --------------------------------------------------------$newline";

$file_len=strlen($backup);

while($tabs = mysql_fetch_row($tables)):
   if ($add_drop_tables = "y"){
   // Include the DROP TABLE IF EXISTS commands.
   $backup .= "--$newline-- ".__("Table structure for")." `$tabs[0]`".$newline."--".$newline."DROP TABLE IF EXISTS `$tabs[0]`;".$newline."CREATE TABLE IF NOT EXISTS `$tabs[0]` (".$newline;
    } else {
    // Include DROP TABLE IF EXISTS commands only as comments.
   $backup .= "--$newline-- ".__("Table structure for")." `$tabs[0]`".$newline."--".$newline."-- DROP TABLE IF EXISTS `$tabs[0]`;".$newline."CREATE TABLE IF NOT EXISTS `$tabs[0]` (".$newline;
    }
    
   $res = mysql_query("SHOW CREATE TABLE $tabs[0]");
   //echo "<tr><td>". __($tabs[0])."</td><td>$tabs[0]</td></tr>";

    $table_len=strlen($backup)-$file_len;
    $file_len=strlen($backup);
    echo "<tr bgcolor=\"" . $bgcolor . "\">
           <td>$tabs[0]</td><td>".$table_len." ".__("Bytes")."</td>
           <td align=\"center\"><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td>
          </tr>";
   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   while($all = mysql_fetch_assoc($res)):
       $str = str_replace("CREATE TABLE `$tabs[0]` (", "", $all['Create Table']);
       $str = str_replace(",", ", ".$newline."", $str);
       $str2 = str_replace("`) ) TYPE=MyISAM ", "`)".$newline." ) TYPE=MyISAM ", $str);
       $backup .= $str2.";".$newline;
       //" AUTO_INCREMENT=".$tbl_stat[$tabs[0]].";".$newline.$newline;
   endwhile;
   $backup .= "--$newline-- ".__("All Data from table")." `$tabs[0]`".$newline."--".$newline.$newline;
   $data = mysql_query("SELECT * FROM $tabs[0]");
   while($dt = mysql_fetch_row($data)):
            $this_value =  ereg_replace("/","//", $dt[0]);
         $this_value = ereg_replace("'","/'",$this_value);
        $last_value = $this_value ;
   
       $backup .= "INSERT INTO `$tabs[0]` VALUES('".mysql_escape_string($this_value)."'";
       for($i=1; $i<sizeof($dt); $i++):
           $backup .= ", '".mysql_escape_string($dt[$i])."'";
       endfor;
       $backup .= ");".$newline;
   endwhile;
   $backup .= $newline."-- ----------------".__("End of table")."----------------------------".$newline;
endwhile;
$backup .= $newline."-- ----------------".__("End of backup")."----------------------------".$newline.$newline;
// Let's make sure the file exists and is writable first.

echo "  <tr><td colspan=\"3\"><hr /></td></tr>\n";

if (is_writable($backup_filename)) {

   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   if (!$handle = fopen($backup_filename, 'w+')) {
         echo "<tr><td colspan=\"3\" bgcolor=\"" . $bgcolor . "\">".__("Could not open file (").$backup_filename.")</td></tr>\n";
         exit;
   }else{
       echo "<tr>\n";
        echo "<td bgcolor=\"" . $bgcolor . "\">".__("Create File")." '".$backup_filename."'</td>\n";
        echo "<td bgcolor=\"" . $bgcolor . "\"></td>\n";
        echo "<td align=\"center\" bgcolor=\"" . $bgcolor . "\"><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td>\n";
       echo "</tr>\n";
   }

   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   // Write $somecontent to our opened file.
   if (fwrite($handle, $backup) === FALSE) {
       echo "<tr><td colspan=\"3\" bgcolor=\"" . $bgcolor . "\">".__("Could not write to file (").$backup_filename.")</td></tr>\n";
       exit;
   }else{
       echo "<tr>\n";
        echo "<td bgcolor=\"" . $bgcolor . "\">".__("Write File")." '".$backup_filename."'</td>\n";
        echo "<td bgcolor=\"" . $bgcolor . "\"></td>\n";
        echo "<td align=\"center\" bgcolor=\"" . $bgcolor . "\"><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td>\n";
       echo "</tr>\n";
   }
   $database_length = strlen($backup);
   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);

   echo "<tr>\n";
    echo "<td bgcolor=\"" . $bgcolor . "\">".__("Success, wrote ").$database_length." ".__("bytes to file")."</td>\n";
    echo "<td bgcolor=\"" . $bgcolor . "\"></td>\n";
    echo "<td align=\"center\" bgcolor=\"" . $bgcolor . "\"><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td>\n";
   echo "</tr>\n";

   fclose($handle);

   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   echo "<tr bgcolor=\"" . $bgcolor . "\"><td colspan=\"3\">&nbsp;</td></tr>\n";
   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   echo "<tr bgcolor=\"" . $bgcolor . "\"><td class=\"contentsubtitle\">".__("Backup Completed")."</td><td></td><td></td></tr>\n";
   echo "<tr>";
    echo "<td>".__("Database Backed up in")." ".number_format((microtime_float()-$time_start),2)." ". __("Seconds").". </td>\n";
    echo "<td bgcolor=\"" . $bgcolor . "\"></td>\n";
    echo "<td align=\"center\"><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td>\n";
   echo "</tr>\n";
} else {
   echo "<tr><td colspan=\"3\">".__("The file")." ".$backup_filename." ".__("is not writable")."</td></tr>\n";
}

echo "</tr></td></table>\n";

include "include_right_column.php";

echo "</body>\n</html>\n";



?>
