<?php 
$page = "";
$extra = "";
$software = "";
$count = 0;
$total_rows = 0;

$page = "calls";
include "include.php"; 

$title = "call_user_datails.php";
if (isset($_GET["show_all"])){ $count_system = '10000'; } else {}
if (isset($_GET["page_count"])){ $page_count = $_GET["page_count"]; } else { $page_count = 0;}
$page_prev = $page_count - 1;
if ($page_prev < 0){ $page_prev = 0; } else {}
$page_next = $page_count + 1;
$page_current = $page_count;
$page_count = $page_count * $count_system;

echo "<td>\n";


$user_name = "";

if (isset($_GET['name'])) {$name = $_GET['name'];} else {$name= "none";}
if (isset($_GET['show_details'])) {$show_details = $_GET['show_details'];} else {$show_details= "basic";}

if ($use_ldap_integration == "y") {

// Find name from domain\name
list ($name1,$name2,$name3) = split('['.chr(92).']', $name);

if (strlen($name3)!=0) {
$name = $name3 ;
}else{
$name = $name1 ;
}




// This throws away some spurious Active Direcrory error related nonsense if you have no phone number or whatever
// should really catch this gracefully
error_reporting(0);

//
//Note that this LDAP string specifies the OU that contains the User Accounts
//All OUs under it are also retrieved
$dn = $ldap_base_dn;

//domain user fullname and password

$user = $ldap_user;
$secret = $ldap_secret;
//$name="*".$name;
$attributes = array("displayname","description","userprincipalname","homedirectory","homedrive","profilepath","scriptpath","mail","samaccountname","telephonenumber","usncreated","department","sn");
//$filter = "(&(objectClass=user)(objectCategory=person)((samaccountname=".$name.")(name=".$name.")(displayname=".$name.")(cn=".$name."))";
$filter = "(&(objectClass=user)(objectCategory=person)(|(samaccountname=".$name.chr(42).")(name=".$name.chr(42).")(displayname=".$name.chr(42).")(cn=".$name.chr(42).")))";
//(|(name=$name*)(displayname=$name*)(cn=$name*))


$ad = ldap_connect($ldap_server) or die(__("Couldn't connect to LDAP Dirctory"));
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
$bd = ldap_bind($ad,$user,$secret);
if ($bd){
  //echo "Admin - Authenticated<br>";
} else {
  echo "Problem - Not a valid username/password.";
}


ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
$bd = ldap_bind($ad,$user,$secret);
if ($bd){
  //echo "Admin - Authenticated<br>";
} else {
  echo "Problem - Not a valid username/password.";
}



if ($show_details == "basic"){$result = ldap_search($ad, $dn, $filter, $attributes);}
    else
    {$result = ldap_search($ad, $dn, $filter);}


//$result = ldap_search($ad, $dn, $filter);
ldap_sort($ad,$result,"displayname");
$entries = ldap_get_entries($ad, $result);



echo "<div class=\"main_each\">\n";

echo "<form action=\"call_users_details.php?sub=no\" method=\"post\">";
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"content\">";

$num_found = $entries["count"];
for ($user_record_number = 0; $user_record_number<$num_found; $user_record_number++) {
//echo "Next User:<br>";
$record_number = $user_record_number+1;
echo "<td><img src='images/users_l.png' width='64' height='64' alt='' /></td><td>".__("Domain User Account Details Like <b>".$name."</b>")." $record_number of $num_found ";
	

      if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
	echo "<tr bgcolor=\"" . $bgcolor . "\"><td><h3>" . $entries[$user_record_number]["displayname"][0] . "</h3></td><td></td></tr>";
    	echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>Telephone:</td><td>" . $entries[$user_record_number]["telephonenumber"][0] . "</a></b></td></tr>";	
	  if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
 	echo "<tr bgcolor=\"" . $bgcolor . "\"><td>" .__("Full LDAP Account Details"). "</td><td></td></tr>";      
 for ($user_record_field_number=0; $user_record_field_number<$entries[$user_record_number]["count"]; $user_record_field_number++){
     $data =$entries[$user_record_number][$user_record_field_number];

     for ($user_record_field_number_data=0; $user_record_field_number_data<$entries[$user_record_number][$data]["count"]; $user_record_field_number_data++) {
     if  (isEmailAddress($entries[$user_record_number][$data][$user_record_field_number_data])){
          // If its a valid email address, highlight it, and add a URL mailto:
      if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }	
     echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>".__($data)."</b></td><td><a href='mailto:" . $entries[$user_record_number][$data][$user_record_field_number_data] . "'>" . $entries[$user_record_number][$data][$user_record_field_number_data] . "</a></td></tr>";
     }
     else 
     {
            // Else just show it. 
      	  if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
          echo "<tr bgcolor=\"" . $bgcolor . "\"><td>".__($data)."</td><td>" .$entries[$user_record_number][$data][$user_record_field_number_data]. "</td></tr>";
      }    
     }
  }
echo "<p>"; // separate entries
}

} else {

        echo "<div class=\"main_each\">\n";
        echo "<form action=\"call_users_details.php?sub=no\" method=\"post\">";
        echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" class=\"content\">";
     	if ($bgcolor == "#F1F1F1") { $bgcolor = "#FFFFFF"; } else { $bgcolor = "#F1F1F1"; }
        echo "<p>"; 
        echo "<tr bgcolor=\"" . $bgcolor . "\"><td><b>".__("LDAP Not configured. Please set this up in Admin> Config")."</b></td></tr>";


//        echo "<tr>".__("LDAP Not configured. Please set this up in Admin> Config")."</tr>";
}
echo "</table>";

echo "</td>\n";

include "include_right_column.php";

include "include_png_replace.php";

echo "</body>\n</html>\n";


?>