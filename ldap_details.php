<?php 
$page = "";
$extra = "";
$software = "";
$count = 0;
$total_rows = 0;

$page = "";
include "include.php"; 

$title = "ldap_datails.php";
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

echo "<td>\n";


$user_name = "";
// Set name from URL
if (isset($_GET['name'])) {$name = $_GET['name'];} else {$name= "none";}
// Set record type, supports comupter or user accepts anything !FIXME this is so we can select DNS entries or whatever as yet uncoded.
if (isset ($_GET['record_type'])) {$record_type = $_GET['record_type'];} else {$record_type="user";}
// Sets detail level
if (isset($_GET['full_details'])) {$full_details = $_GET['full_details'];} else {$full_details= "n";}
// Sets inject into database (if supported  for record type). 
if (isset($_GET['inject'])) {$inject = $_GET['inject'];} else {$inject= "n";}
// Sets the sort field. 
if (isset($_GET['$sort_column'])) {$sort_column = $_GET['$sort_column'];} else {$sort_column= "none";}



// Check setup included ldap integration
if ($use_ldap_integration == "y") {

// Find name from domain\name
if ($record_type=="user"){
$slash_char = chr(92);

$pos = strrpos($name, $slash_char);

if ($pos === false ) {
    // Dont need to do anything if we didn't find a slash in the username.
    } else {
    // We pick up the right half of the string  if we found the slash
    $pos=$pos+1;
    $name = substr($name,($pos));
//   echo $name;
    }
 }   
// $ldap vars are set in config
//
//Note that this LDAP string specifies the OU that contains the User Accounts
//All OUs under it are also retrieved

$dn = $ldap_base_dn;

//domain user fullname and password

$user = $ldap_user;
$secret = $ldap_secret;

if ($record_type=="user"){
//$attributes = array("displayname","description","userprincipalname","homedirectory","homedrive","profilepath","scriptpath","mail","samaccountname","telephonenumber","location","department","sn","badpwdcount");
$attributes = array("displayname","mail","telephonenumber","location","department","sn");

$filter = "(&(objectClass=user)(objectCategory=person)(|(samaccountname=".$name.chr(42).")(name=".$name.chr(42).")(displayname=".$name.chr(42).")(cn=".$name.chr(42).")))";

if ($full_details == 'dump') {$filter = "(&(objectCategory=person)(objectClass=user)(telephonenumber=*))";}
} 
if ($record_type=="computer"){
$attributes = array("name","description","operatingsystem","operatingsystemservicepack","operatingsystemversion","location");
$filter = "(&(objectClass=computer)(objectCategory=computer)(|(samaccountname=".$name.chr(42).")(name=".$name.chr(42).")(displayname=".$name.chr(42).")(cn=".$name.chr(42).")))";
}


// This throws away some spurious Active Direcrory error related nonsense if you have no phone number or whatever
// should really catch this gracefully
error_reporting(0);

if (function_exists('ldap_connect')){
$ad = ldap_connect($ldap_server) or die(__("Couldn't connect to LDAP Dirctory"));
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
$bd = ldap_bind($ad,$user,$secret);

if ($bd){
  //echo "Admin - Authenticated<br>";
} else {
  echo "<b>".__("Problem - Not a valid username/password.")."</b>";
}


ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
$bd = ldap_bind($ad,$user,$secret);
if ($bd){
// Could display a connected message here, but that messes up the formatting. 
} else {
  echo "<b>".__("Problem - Not a valid username/password.")."</b>";
}
} else {

        echo "<b>".__("LDAP connectivity is not available, please check php.ini ")."</b>";
}

if ($full_details == "n"){$result = ldap_search($ad, $dn, $filter, $attributes);}
    else
    {$result = ldap_search($ad, $dn, $filter);}

if  ((isset($sort_column)) and ($sort_column !="none")){
ldap_sort($ad,$result,"displayname");
}

$entries = ldap_get_entries($ad, $result);

echo "<div class=\"main_each\">\n";
echo "<form action=\"search.php?sub=no\" method=\"post\">";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"content\">";

$num_found = $entries["count"];

if ($num_found == 0 ){
        echo "<div class=\"main_each\">\n";
        echo "<form action=\"search.php?sub=no\" method=\"post\">";
        echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"content\">";
        if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
        echo "<p>"; 
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>".__("Not found in ".$ldap_base_dn.".")."</b></td></tr>";

} else {

if ($inject == "y"){
//sql inject create table    $table_name = "ldap_users_details";
    $column_names = "";
    $column_values = "";
    $table_name = "ldap_users_details";
    
//$sql ="DROP TABLE IF EXISTS `".$table_name ."`;";
//$result = mysql_query($sql) ;

  $time_now = time(); 
  $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
  `ldap_users_details_id` int(11) NOT NULL auto_increment,
    `ldap_users_details_first_timestamp` bigint(20) unsigned NOT NULL default '".$time_now."',
    `ldap_users_details_update_timestamp` bigint(20) unsigned NOT NULL default '0',
    `samaccountname` varchar(100) NOT NULL default '',
   PRIMARY KEY  (`samaccountname`),
   KEY (`ldap_users_details_id`)
   ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
//      $result = mysql_query($sql);
        $result = mysql_query($sql) or die ('CREATE Failed: ' . mysql_error() . '<br />' . $sql);
//end
} 

for ($user_record_number = 0; $user_record_number<$num_found; $user_record_number++) {
//echo "Next User:<br>";

$record_number = $user_record_number+1;

// Show the correct image
if ($record_type == 'computer'){
        echo "<td><img src='images/o_terminal_server.png' width='64' height='64' alt='' />";
        }
        if ($record_type == 'user'){
        echo "<td><img src='images/users_l.png' width='64' height='64' alt='' />";
        }
        
   
        
        
        $bgcolor == "#FFFFFF";	
//      if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td><h3>" . $entries[$user_record_number]["displayname"][0] . "</h3></td><td></td></tr>";
	  $bgcolor = change_row_color($bgcolor,$bg1,$bg2); 
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>Telephone:</td><td>" . $entries[$user_record_number]["telephonenumber"][0] . "</a></b></td></tr>";	
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2); 
      echo "<tr bgcolor=\"" . $bgcolor . "\"><td>" .__("Full LDAP Account Details"). "</td><td></td></tr>";      
      for ($user_record_field_number=0; $user_record_field_number<$entries[$user_record_number]["count"]; $user_record_field_number++){
      $data =$entries[$user_record_number][$user_record_field_number];


    
  
    for ($user_record_field_number_data=0; $user_record_field_number_data<$entries[$user_record_number][$data]["count"]; $user_record_field_number_data++) {

if ($inject == "y"){
// SQL inject code.

//        $sql="ALTER TABLE 'ldap_users_details' ADD COLUMN IF NOT EXISTS '$data' varchar(255) ;";
        $sql2="ALTER TABLE ".$table_name ." ADD COLUMN ".$data." varchar(255) NOT NULL default '' ;";

        $result = mysql_query($sql2) ;
        //or die ('ALTER Failed: ' . mysql_error() . '<br />' . $sql);
        
         $column_names = $column_names.$data.",";
         
         $this_value =  ereg_replace("/","-", $entries[$user_record_number][$data][$user_record_field_number_data]);
         $this_value = ereg_replace("'","-",$this_value);
        $last_value = $this_value ;
        
        $column_values = $column_values."'".$this_value."',";
}
// End SQL inject        
        if  (isEmailAddress($entries[$user_record_number][$data][$user_record_field_number_data])){
          // If its a valid email address, highlight it, and add a URL mailto:
      $bgcolor = change_row_color($bgcolor,$bg1,$bg2); 	
     echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>".__($data).":</b></td><td><a href='mailto:" . $entries[$user_record_number][$data][$user_record_field_number_data] . "'>" . $entries[$user_record_number][$data][$user_record_field_number_data] . "</a></td></tr>";
     }
     else 
     {
        if  (isGUID($entries[$user_record_number][$data][$user_record_field_number_data])){
           $guid_text= strtoupper(formatGUID($entries[$user_record_number][$data][$user_record_field_number_data]));
           echo "<tr bgcolor=\"" . $bgcolor . "\"><td>".__($data).":</td><td>{".$guid_text."}</td></tr>";
         }
         else
         {
         if  (isSID($data)){
           $sid_text= strtoupper(formatSID($entries[$user_record_number][$data][$user_record_field_number_data]));
           echo "<tr bgcolor=\"" . $bgcolor . "\"><td>".__($data).":</td><td>{".$sid_text."}</td></tr>";
            }
         else
         {
            // Else just show it. 
          $bgcolor = change_row_color($bgcolor,$bg1,$bg2); 
           echo "<tr bgcolor=\"" . $bgcolor . "\"><td>".__($data).":</td><td>" .$entries[$user_record_number][$data][$user_record_field_number_data]. "</td></tr>";
         }
        }         
    }
     
  }
 
}
  if ($inject == "y"){
  // SQL inject code
            $column_names = rtrim( $column_names,",");
            $column_values = rtrim( $column_values,",");
            $time_now = time();
           $sql="INSERT INTO ".$table_name. " (".$column_names.") VALUES (".$column_values.") ON DUPLICATE KEY UPDATE ldap_users_details_update_timestamp = ".$time_now." ;";

        //
        $result = mysql_query($sql) or die ('Insert Failed: ' . mysql_error() . '<br />' . $sql); 
               $column_names = "";
               $column_values = "";
  // End SQL inject
} 
               
  echo "<p>"; // separate entries
  echo "<tr><td colspan=\"2\"><hr /></td></tr>\n";
 }
}
} else {

        echo "<div class=\"main_each\">\n";
        echo "<form action=\"search.php?sub=no\" method=\"post\">";
        echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"content\">";
       $bgcolor = change_row_color($bgcolor,$bg1,$bg2); 
        echo "<p>"; 
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>".__("LDAP Not configured. Please set this up in Admin> Config")."</b></td></tr>";
}
echo "</table>";

echo "</td>\n";
// Unbind LDAP again to avoid flooding it with connections. 
ldap_unbind($ad);
include "include_right_column.php";

echo "</body>\n</html>\n";


?>
