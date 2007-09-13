<?php
include_once "include_config.php";
if ((isset($use_ldap_login) and ($use_ldap_login == 'y'))) {
include "include_ldap_login.php";
}else {}
include_once "include_lang.php";
include_once "include_functions.php";
include_once "include_col_scheme.php";
$is_refreshable = false ;
$refresh_period = 10;
$jscript_count = 0;
if ($show_other_discovered == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_system_discovered == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_systems_not_audited == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_partition_usage == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_software_detected == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_patches_not_detected == 'y'){ $jscript_count = $jscript_count + 1; }
if ($show_detected_servers == 'y'){ $jscript_count = $jscript_count + 5; }

if (!isset($page)) { $page = ""; }

if ($page == "add_pc"){$use_pass = "n";}

if ($use_pass != "n") {
  // If there's no Authentication header, exit
  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('This page requires authentication');
  }
  // If the user name doesn't exist, exit
  if (!isset($users[$_SERVER['PHP_AUTH_USER']])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('Unauthorized!');
  }
  // Is the password doesn't match the username, exit
  if ($users[$_SERVER['PHP_AUTH_USER']] != md5($_SERVER['PHP_AUTH_PW']))
  {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="PHP Secured"');
    exit('Unauthorized!');
  }
} else {}


if (isset($use_https) AND $use_https == "y") {


#gets the URI of the script
//$our_url =  $_SERVER['SCRIPT_URI'];

#chops URI into bits 
//$chopped = parse_url($our_url);

#HOST and PATH portions of your final destination
//$destination = $chopped[host].$chopped[path];
//print_r ($chopped[host]); 
/*
#if you are not HTTPS, then do something about it
if($chopped[scheme] != "https"){
/*
    #forwards to HTTP version of URI with secure certificate
    header("Location: https://$destination");
    exit(); 
    }
*/

       if ($_SERVER["SERVER_PORT"]!=443){ header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']); exit(); }
}


// ob_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<?php
if ($is_refreshable) {

    #gets the URI of the script
    $our_url =  $_SERVER['SCRIPT_URI'];

    #chops URI into bits 
    $chopped = parse_url($our_url);

    #HOST and PATH portions of your final destination
    $destination = $chopped[scheme]."://".$chopped[host].$chopped[path];
    echo " <META HTTP-EQUIV=REFRESH CONTENT=".$refresh_period.";URL=".$destination.">";
        }
?>        
    <title>Open-AudIT</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" type="text/css" href="default.css" />
    <script type="text/javascript">
      /*<![CDATA[*/
      function IEHoverPseudo() {
        var navItems = document.getElementById("primary-nav").getElementsByTagName("li");
        for (var i=0; i<navItems.length; i++) {
          if(navItems[i].className == "menuparent") {
            navItems[i].onmouseover=function() { this.className += " over"; }
            navItems[i].onmouseout=function() { this.className = "menuparent"; }
          }
        }
      }

          window.onload = IEHoverPseudo;

      /*]]>*/
    </script>

    <script type="text/javascript">
      <!--
      function switchUl(id){
        if(document.getElementById){
          a=document.getElementById(id);
          a.style.display=(a.style.display!="none")?"none":"block";
        }
      }
      // -->
    </script>

  </head>
  <body>
<?php

$sub = "0";
$pc = "";

if (isset($_GET['pc'])) { $pc = $_GET['pc'];   } else { }
if (isset($_GET['sub'])) { $sub = $_GET['sub']; } else { $sub="all"; }
if (isset($_GET['sort'])) { $sort = $_GET['sort']; } else { $sort="system_name"; }

$mac = $pc;

if ($page <> "setup"){
  $db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
  mysql_select_db($mysql_database,$db);
  $SQL = "SELECT config_value FROM config WHERE config_name = 'version'";
  $result = mysql_query($SQL, $db);

  if ($myrow = mysql_fetch_array($result)){
    $version = $myrow["config_value"];
  } else {}
} else {
  $version = "0.1.00";
}
/*
?>
<table width="100%">
<td colspan="3" class="main_each">
<?php
*/
    $page_type="standard";
    if (strpos($_SERVER['REQUEST_URI'],"admin")){
    $page_type="admin";
    } 
    if (strpos($_SERVER['REQUEST_URI'],"input") or strpos($_SERVER['REQUEST_URI'],"pc_add")){
    $page_type="input";
    } 
    if (strpos($_SERVER['REQUEST_URI'],"system")){
    $page_type="system";
    } 
    if (strpos($_SERVER['REQUEST_URI'],"list")){
    $page_type="list";
    } 
 
if ((isset($use_ldap_login) and ($use_ldap_login == 'y') and ($page_type <> "input") )) {
    echo "<table width=\"100%\">\n";
    echo "<td colspan=\"3\" class=\"main_each\">\n";
    echo "<a href=\"ldap_logout.php\">".__("Logout ").$_SESSION["username"]."</a>\n";
//  Uncomment the following to see what tyoe of page this is
//    echo "<a href=\"index.php\">"."    We are in a ".$page_type." type of page"."</a>\n";
    echo "</td>\n";
    echo "</table>\n";

} else {}
/*
?>
/
</td>
</table>
*/
?>
<table width="100%">
  <tr>
        <td colspan="3" class="main_each"><a href="index.php"><img src="images/logo.png" width="300" height="48" alt="" style="border:0px;" /></a></td>
  </tr>

  <tr>
    <td style="width:170px;" rowspan="12" valign="top">
      <ul id="primary-nav">
        <li><a href="index.php"><?php echo __("Home"); ?></a></li>
        

<?php
// echo "<li><a href=\"include_ldap_logout.php\">".__("Logout ").$_SESSION["username"]."</a></li>\n";
if ($pc > "0") {
  $sql = "SELECT system_uuid, system_timestamp, system_name, system.net_ip_address, net_domain FROM system, network_card WHERE system_uuid = '$pc' OR system_name = '$pc' OR (net_mac_address = '$pc' AND net_uuid = system_uuid)";
  $result = mysql_query($sql, $db);
  $myrow = mysql_fetch_array($result);
  $timestamp = $myrow["system_timestamp"];
  $GLOBAL["system_timestamp"]=$timestamp;
  $pc = $myrow["system_uuid"];
  $ip = $myrow["net_ip_address"];
  $name = $myrow['system_name'];
  $domain = $myrow['net_domain'];

  //Menu-Entries for the selected PC
  
  require_once("include_menu_array.php");
  echo "<li class=\"menuparent\">".
        "<a href=\"system.php?pc=$pc&amp;view=summary\">".
        "<span>&gt;</span>".
        $name.
        "</a>\n";

   echo "<ul>\n";
    reset ($menue_array["machine"]);
    while (list ($key_1, $topic_item) = each ($menue_array["machine"])) {
        if (isset($topic_item["class"])) {
          echo "<li class=\"".$topic_item["class"]."\">";
        } else {
          echo "<li>";
        }

        echo "<a href=\"".$topic_item["link"]."\">";
        if(isset($topic_item["childs"]) AND is_array($topic_item["childs"])){
          echo "<span><img src=\"images/spacer.gif\" height=\"16\" width=\"0\" alt=\"\" />&gt;</span>";
        }
        echo "<img src=\"".$topic_item["image"]."\" style=\"border:0px;\" alt=\"\" />&nbsp;";
        echo __($topic_item["name"]);
        echo "</a>\n";

        if(isset($topic_item["childs"]) AND is_array($topic_item["childs"])){
          echo "<ul>\n";
          @reset ($topic_item["childs"]);
          while (list ($key_2, $child_item) = @each ($topic_item["childs"])) {
            echo "<li><a href=\"".$child_item["link"]."\"";
            if (isset($topic_item["title"])) {
              echo " title=\"".$topic_item["title"]."\"";
            }
            echo "><img src=\"".$child_item["image"]."\" style=\"border:0px;\" alt=\"\" />&nbsp;";
            echo __($child_item["name"]);
            echo "</a></li>\n";
          }
          echo "</ul>\n";
        }
        echo "</li>\n";
    
    }
    
   echo "</ul>\n";
  echo "</li>\n";
}
    //Normal Menu-Entries
    require_once("include_menu_array.php");
    reset ($menue_array["misc"]);
    while (list ($key_1, $topic_item) = each ($menue_array["misc"])) {
        echo "<li class=\"".$topic_item["class"]."\">";
         echo "<a href=\"".$topic_item["link"]."\"";
          if(isset($topic_item["title"])) {
            echo " title=\"".$topic_item["title"]."\"";
          }
         echo ">";
          if(is_array($topic_item["childs"])){
              echo "<span>&gt;</span>";
          }
          if(isset($topic_item['image']) AND $topic_item["image"]!=""){
              echo "<img src=\"".$topic_item["image"]."\" width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />&nbsp;";
          }
          echo __($topic_item["name"]);
         echo "</a>";
        echo "<ul>\n";

        if(is_array($topic_item["childs"])){
            @reset ($topic_item["childs"]);
            while (list ($key_2, $child_item) = @each ($topic_item["childs"])) {

                echo "<li>";
                 echo "<a href=\"".$child_item["link"]."\" title=\"".$child_item["title"]."\">";
                  if(isset($child_item["childs"]) AND is_array($child_item["childs"])){
                      echo "<span>&gt;</span>";
                  }
                  echo "<img src=\"".$child_item["image"]."\"  width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />&nbsp;";
                  echo __($child_item["name"]);
                 echo "</a>";

                 if(isset($child_item["childs"]) AND is_array($child_item["childs"])){
                    echo "<ul>\n";
                    @reset ($child_item["childs"]);
                    while (list ($key_3, $child_item_2) = @each ($child_item["childs"])) {
                        echo "<li>";
                         echo "<a href=\"".$child_item_2["link"]."\" title=\"".$child_item_2["title"]."\">";
                          echo "<img src=\"".$child_item_2["image"]."\"  width=\"16\" height=\"16\" style=\"border:0px;\" alt=\"\" />&nbsp;";
                          echo __($child_item_2["name"]);
                         echo "</a>";
                        echo "</li>\n";
                    }
                    echo "</ul>\n";
                 }
                echo "</li>\n";
            }
        }

         echo "</ul>\n";
        echo "</li>\n";
    unset($topic_item["title"]);
    }
    if ((isset($use_ldap_login) and ($use_ldap_login == 'y'))) {
//    echo "<li><a href=\"include_ldap_logout.php\">".__("Logout ").$_SESSION["username"]."</a></li>\n";
} else {}
//     
// Add a Strict Test button if $validate is set.     
if ((isset($validate)) and ($validate =="y")){
echo "<p>";
echo "<a href=\"http://validator.w3.org/check/referer\"><img src=\"http://www.w3.org/Icons/valid-xhtml10\" alt=\"Valid XHTML 1.0!\" height=\"31\" width=\"88\" /></a>";
//echo "<script type=\"text/javascript\" language=\"JavaScript1.2\" src=\"http://www.altavista.com/help/free/inc_translate\"></script>";
echo "<noscript><a href=\"http://www.altavista.com/babelfish/tr\"></noscript>";
//echo "<script language=\"JavaScript1.2\" src=\"http://www.altavista.com/static/scripts/translate_engl.js\"></script>\"";
echo "</p>";
//


echo "</body>\n</html>";
}
 echo "</ul>\n";
  echo "</td>\n";

?>
     
