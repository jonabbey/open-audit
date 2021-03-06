<?php
include_once("include_config.php");
include_once("include_functions.php");
include_once("include_lang.php");

// If they selected to email the report, this page is called via AJAX, so set some headers
// then check if SMTP is enabled
if(isset($_GET["email_list"])){
  require("include_email_functions.php");
  error_reporting(0);

  header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
  header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
  header( "Cache-Control: no-cache, must-revalidate" );
  header( "Pragma: no-cache" );
  header("Content-type: text/xml");

  $email =& GetEmailObject();

  if ( is_null($email) ){ exit("<pdfsend><smtpstatus>disabled</smtpstatus></pdfsend>"); }
}

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



$csv_data = '';

//Table head
foreach($viewdef_array["fields"] as $field) {
    if($field["show"]!="n"){
        $csv_data .= $field["head"];
        $csv_data .= "\t";
    }
}
$csv_data .= "\r\n";

//Table body
if ($myrow = mysql_fetch_array($result)){
    do{
        foreach($query_array["fields"] as $field){
            if($field["show"]!="n"){
                $csv_data .= $myrow[$field["name"]];
                $csv_data .= "\t";
            }
        }
        $csv_data .= "\r\n";
    }while ($myrow = mysql_fetch_array($result));
}

// set the filename if specified
$filename = (isset($_GET["filename"])) ? $_GET["filename"] . '.xls' : 'export.xls';

if (!isset($_GET["email_list"])){
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: inline; filename=\"$filename\"");

  exit("$csv_data");
}
else {
  $username = (isset($_GET["username"])) ? $_GET["username"] : "Unknown";
  $time     = date("F j, Y, g:i a");

  $variables = array(
    '{filename}'  => $filename,
    '{filetype}'  => 'CSV',
    '{username}'  => $username,
    '{timestamp}' => $time
  );

  $subject = "Open-AudIT CSV Report";
  $html    = ParseEmailTemplate($variables,'./emails/export_file.html');

  $attachment = array(
   "Data"         => $csv_data,
   "Name"         => $filename,
   "Content-Type" => "application/vnd.ms-excel",
   "Disposition"  => "attachment"
  );

  $result = SendHtmlEmail($subject,$html,$_GET["email_list"],$email,array($attachment),null);

  $xml  = "<csvsend>";
  $xml .= "<smtpstatus>enabled</smtpstatus>";
  $xml .= "<result>";
  $xml .= (count($result) == 0) ? 'true' : 'false';
  $xml .= "</result>";
  foreach($result as $address){ $xml .= "<email>$address</email>"; }
  $xml .= "</csvsend>";

  exit("$xml");
}
?>
