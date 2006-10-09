<?php
//    Open Audit Restore Database
//    restore over current database.
include "include.php";
//$this_page="https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
//echo "<meta name=\"refresh\" content=\"10;".$this_page."\">";
//
$newline = "\r\n";
$page = "database_backup_form.php";
$bgcolor = "#FFFFFF";

set_time_limit(240);


echo "<td valign=\"top\">$newline";
echo "<div class=\"main_each\">$newline";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" >$newline";
echo "  <tr><td class=\"contenthead\">".__("Backup the Database")."</td></tr>";
echo "  <tr><td colspan=\"1\"><hr /></td></tr>";
echo "<tr><td>".__("Select a database")."</td>";
//echo "  <tr><td colspan=\"1\"><hr /></td></tr>";
$today = date("dmYGis");
$backup_dir = '.\\backup\\';

if (!file_exists($backup_dir)) {
   mkdir($backup_dir);
}


// Start of restore section

echo "<form method=\"GET\" action=\"database_backup_sql.php\" name=\"database_backup\">";
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"content\">";
//echo "<tr><td colspan=\"1\"><hr /></td></tr>";
echo "<tr>\n";
echo "<td>".__("Database").":</td>\n";
echo "<td><select size=\"1\" name=\"backup_name\" class=\"for_forms\">\n";


$link = mysql_connect($mysql_server, $mysql_user, $mysql_password);
$db_list = mysql_list_dbs($link);

while ($row = mysql_fetch_object($db_list)) {
    $my_database_names = $row->Database ;
    if($mysql_database==$my_database_names) $selected = "selected"; else $selected = "";
    echo "<OPTION $selected>".$my_database_names."</OPTION>\n";
//  echo '</SELECT>';
}

/*
$handle=opendir('./backup/');
while ($file = readdir ($handle)) {
    if ($file != "." && $file != "..") {
        if(substr($file,strlen($file)-4)==".sql"){
            if($language == substr($file,0,strlen($file)-4) ) $selected="selected"; else $selected="";
            echo "<option $selected>".substr($file,0,strlen($file)-4)."</option>\n";
        }
    }
}
//
//closedir($handle);
//echo "<tr><td colspan=\"5\"><hr /></td></tr>\n";
*/
echo "<tr><td><input type=\"submit\" value=\"".__("Backup")."\" name=\"submit_button\" /></td></tr>\n";

echo "<tr><td colspan=\"2\"><br>".__("This process can take several minutes")."</td></tr>\n";

//End of restore section
echo "</tr></td></table>\n";

include "include_right_column.php";

include "include_png_replace.php";

echo "</body>\n</html>\n";



?>