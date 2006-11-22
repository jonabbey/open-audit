<?php
include_once("include_config.php");
include_once("include_functions.php");
include_once("include_lang.php");

//MySQL-Connect
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

//Include the view-definition
if(isset($_REQUEST["view"]) AND $_REQUEST["view"]!=""){
    $include_filename = "list_viewdef_".$_REQUEST["view"].".php";
}else{
    $include_filename = "list_viewdef_all_systems.php";
}
if(is_file($include_filename)){
    include_once($include_filename);
    $viewdef_array=$query_array;
}else{
    die("FATAL: Could not find view $include_filename");
}

    //Executing the Qeuery
    $sql=urldecode($_REQUEST["sql"]);
    $result = mysql_query($sql, $db);
    if(!$result) {die( "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>" );};
    $this_page_count = mysql_num_rows($result);


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: inline; filename=\"export.xls\"");

//Table head
foreach($viewdef_array["fields"] as $field) {
    if($field["show"]!="n"){
        echo $field["head"];
        echo "\t";
    }
}
echo "\r\n";

//Table body
if ($myrow = mysql_fetch_array($result)){
    do{
        foreach($query_array["fields"] as $field){
            if($field["show"]!="n"){
                echo $myrow[$field["name"]];
                echo "\t";
            }
        }
        echo "\r\n";
    }while ($myrow = mysql_fetch_array($result));
}

?>
