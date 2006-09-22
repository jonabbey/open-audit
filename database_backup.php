<?php
//    Open Audit Backup Database
//    backup to folder with current timestamp. 
include "include.php";
//$this_page="https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; 
//echo "<meta name=\"refresh\" content=\"10;".$this_page."\">";
//
$newline = "\r\n";
$page = "database_backup.php";
$bgcolor = "#FFFFFF";
set_time_limit(240);


echo "<td valign=\"top\">$newline";
echo "<div class=\"main_each\">$newline";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" >$newline";
echo "  <tr><td class=\"contenthead\">".__("Backing up the Database")."</td></tr>";
echo "  <tr><td colspan=\"3\"><hr /></td></tr>";
echo "<tr><td>".__("The following tables were found")."</td><td>".__("Length")."</td><td>".__("Connectable")."</td></tr>";
echo "  <tr><td colspan=\"3\"><hr /></td></tr>";
$today = date("dmYGis");
$backup_dir = '.\\backup\\';

if (!file_exists($backup_dir)) {
   mkdir($backup_dir);
} 
 

$backup_filename = $backup_dir.'backup'.$today.'.sql';
$handle = fopen($backup_filename, "w");
$table_len=strlen($backup);

$tab_status = mysql_query("SHOW TABLE STATUS");
while($all = mysql_fetch_assoc($tab_status)):
   $tbl_stat[$all[Name]] = $all[Auto_increment];
endwhile;
unset($backup);
$tables = mysql_list_tables($mysql_database);
$date_time = date('l dS \of F Y h:i:s A');

$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$backup .= "-- ----------  $date_time   -----------$newline";
$backup .= "-- ----------  ".__("Open Audit Database Backup")."  -----------$newline";
$backup .= "-- ----".$mysql_database."----";
$backup .= "-- $url --$newline";
$backup .= "-- --------------------------------------------------------$newline";

$file_len=strlen($backup);

while($tabs = mysql_fetch_row($tables)):
   $backup .= "--$newline-- ".__("Table structure for")." `$tabs[0]`".$newline."--".$newline."DROP TABLE IF EXISTS `$tabs[0]`;".$newline."CREATE TABLE IF NOT EXISTS `$tabs[0]` (".$newline;
   
   $res = mysql_query("SHOW CREATE TABLE $tabs[0]");
   //echo "<tr><td>". __($tabs[0])."</td><td>$tabs[0]</td></tr>";
    
    $table_len=strlen($backup)-$file_len;
    $file_len=strlen($backup);
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$tabs[0]</td><td>".$table_len." ".__("Bytes")."</td><td><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td></tr>";
   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   while($all = mysql_fetch_assoc($res)):
       $str = str_replace("CREATE TABLE `$tabs[0]` (", "", $all['Create Table']);
       $str = str_replace(",", ", $newline", $str);
       $str2 = str_replace("`) ) TYPE=MyISAM ", "`)".$newline." ) TYPE=MyISAM ", $str);
       $backup .= $str2.";".$newline;
       //" AUTO_INCREMENT=".$tbl_stat[$tabs[0]].";".$newline.$newline;
   endwhile;
   $backup .= "--$newline-- ".__("All Data from table")." `$tabs[0]`".$newline."--".$newline.$newline;
   $data = mysql_query("SELECT * FROM $tabs[0]");
   while($dt = mysql_fetch_row($data)):
       $backup .= "INSERT INTO `$tabs[0]` VALUES('$dt[0]'";
       for($i=1; $i<sizeof($dt); $i++):
           $backup .= ", '$dt[$i]'";
       endfor;
       $backup .= ");".$newline;
   endwhile;
   $backup .= $newline."-- ----------------".__("End of table")."----------------------------".$newline;
endwhile;
$backup .= $newline."-- ----------------".__("End of backup")."----------------------------".$newline.$newline;
// Let's make sure the file exists and is writable first.

if (is_writable($backup_filename)) {

   if (!$handle = fopen($backup_filename, 'w+')) {
         echo "<tr><td>".__("Could not open file (").$backup_filename.")</td></tr>";
         exit;
   }

   // Write $somecontent to our opened file.
   if (fwrite($handle, $backup) === FALSE) {
       echo "<tr><td>".__("Could not write to file (").$backup_filename.")</td></tr>";
       exit;
   }
  $database_length = strlen($backup);
//      $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   echo "  <tr><td colspan=\"3\"><hr /></td></tr>";
   echo "<tr bgcolor=\"" . $bgcolor . "\"><td class=\"contenthead\">".__("Success, wrote ").$database_length." ".__("bytes to file").$backup_filename."</td><td></td><td></td></tr>";
  
   fclose($handle);
   
//     $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   echo "<tr bgcolor=\"" . $bgcolor . "\"><td class=\"contenthead\">".__("Backup Completed")."</td><td></td><td></td></tr>";
} else {
   echo "<tr><td>".__("The file $backup_filename is not writable")."</td></tr>";
}

echo "</tr></td></table>\n";

include "include_right_column.php";

include "include_png_replace.php";

echo "</body>\n</html>\n";



?>
