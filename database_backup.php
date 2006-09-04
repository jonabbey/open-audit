<?php
//    Open Audit Backup Database
//    backup to folder with current timestamp. 
include "include.php";

//
$page = "database_backup.php";
$bgcolor = "#FFFFFF";
set_time_limit(240);


echo "<td valign=\"top\">\n";
echo "<div class=\"main_each\">\n";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" >\n";
echo "  <tr><td class=\"contenthead\">".__("Backing up the Database")."</td></tr>";
echo "  <tr><td colspan=\"3\"><hr /></td></tr>";
echo "<tr><td>".__("The following tables were found")."</td><td>".__("Length")."</td><td>".__("Connectable")."</td></tr>";
$today = date("GisdmY");
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
$tables = mysql_list_tables('openaudit');
$date_time = date('l dS \of F Y h:i:s A');

$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$backup .= "\n-- ----------  $date_time   -----------\n\n";
$backup .= "\n-- ----------  ".__("Open Audit Database Backup")."  -----------\n\n";
$backup .= "\n-- $url --\n\n";
$backup .= "\n-- --------------------------------------------------------\n\n";

$file_len=strlen($backup);

while($tabs = mysql_fetch_row($tables)):
   $backup .= "--\n-- ".__("Table structure for")." `$tabs[0]`\n--\n\nDROP IF EXISTS TABLE `$tabs[0]`\nCREATE TABLE IF NOT EXISTS `$tabs[0]` (&nbsp;";
   
   $res = mysql_query("SHOW CREATE TABLE $tabs[0]");
   //echo "<tr><td>". __($tabs[0])."</td><td>$tabs[0]</td></tr>";
    
    $table_len=strlen($backup)-$file_len;
    $file_len=strlen($backup);
    echo "<tr bgcolor=\"" . $bgcolor . "\"><td>$tabs[0]</td><td>".$table_len." ".__("Bytes")."</td><td><img src=\"images/button_success.png\" width=\"16\" height=\"16\" /></td></tr>";
   $bgcolor = change_row_color($bgcolor,$bg1,$bg2);
   while($all = mysql_fetch_assoc($res)):
       $str = str_replace("CREATE TABLE `$tabs[0]` (", "", $all['Create Table']);
       $str = str_replace(",", ",&nbsp;", $str);
       $str2 = str_replace("`) ) TYPE=MyISAM ", "`)\n ) TYPE=MyISAM ", $str);
       $backup .= $str2." AUTO_INCREMENT=".$tbl_stat[$tabs[0]].";\n\n";
   endwhile;
   $backup .= "--\n-- ".__("All Data from table")." `$tabs[0]`\n--\n\n";
   $data = mysql_query("SELECT * FROM $tabs[0]");
   while($dt = mysql_fetch_row($data)):
       $backup .= "INSERT INTO `$tabs[0]` VALUES('$dt[0]'";
       for($i=1; $i<sizeof($dt); $i++):
           $backup .= ", '$dt[$i]'";
       endfor;
       $backup .= ");\n";
   endwhile;
   $backup .= "\n-- --------------------------------------------------------\n\n";
endwhile;


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
   
   echo "<tr><td>".__("Success, wrote ").$database_length." ".__("bytes to file").$backup_filename."</td></tr>";
  
   fclose($handle);
   echo "<tr><td>".__("Backup Completed")."</td></tr>";
} else {
   echo "<tr><td>".__("The file $backup_filename is not writable")."</td></tr>";
}

echo "</tr></td></table>\n";

include "include_right_column.php";

include "include_png_replace.php";

echo "</body>\n</html>\n";



?>
