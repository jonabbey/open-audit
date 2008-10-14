<?php
/**
*
* @version $Id: ldap_login.php  6th Dec 2007
*
* @author The Open Audit Developer Team
* @objective Index Page for Open Audit.
* @package open-audit (www.open-audit.org)
* @copyright Copyright (C) open-audit.org All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see ../gpl.txt
* Open-Audit is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See www.open-audit.org for further copyright notices and details.
*
*/ 

session_start();
// Include LDAP settings from config file
include "include_config.php";
include "include_lang.php";
if (($_SERVER["SERVER_PORT"]!=443) && ($use_https == "y")){ header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']); exit(); }
// Set variables to those defined in include_config.php
$server = $ldap_server;
$domain = $management_domain_suffix;
$basedn = $ldap_base_dn;

// When you view Active Directory with using LDAP, the Members attribute is not populated with the Primary group.
// Domain Users is the primary group by default. The $admin_list and $user_list variables cannot contain the Primary group.

// You can assign roles by populating these arrays with Active Directory user or group names enclosed in quotes
// and separated separated by commas. If each is empty "array()", all users are given the role of admin.
//$admin_list = array("openauditadmin", "administrator");
//$user_list = array("username1", "username2");

$admin_list = array();
$user_list = array();

// Page to redirect to for initial logon or failed logon
$script=$_SERVER['SCRIPT_NAME'];

// Page to redirect to after successful authentication
$indexpage = "./index.php";

if (isset($_POST['username'])) {
    // Get username and password information from POST
    $username=$_POST['username'];

    //The username must include the Active Directory UPN suffix
    //Remove domain prefix
    $pre = explode(chr(92),$username,2);
    if (isset($pre[1])) {
        $username=$pre[1];
    }
    //Check for domain suffix
    $suf = explode(chr(64),$username,2);
    if (isset($suf[1])) {
        $username_plus_upn = $username;
        $username = $suf[0];
    } else {
        //Add domain suffix
        $username_plus_upn = $username . "@" . $domain;
    }

    $password=$_POST['password'];
    $connect = ldap_connect($server);
    // Connect
    if (!($connect)) {
        session_destroy();
        header('Location: '.$script);
        die ("Could not connect to LDAP server");
    }
    // Set AD specific options
    ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
    // Bind to directory using username and password
    error_reporting(E_ERROR | E_PARSE);
    $bind = ldap_bind($connect, $username_plus_upn, $password);
    if (!($bind) || ($username=="") || ($password=="")) {
        session_destroy();
        // Close the connection
        ldap_unbind($connect);
        header('Location: '.$script.'?Result=Failed');
        die ("Could not bind with account $username");
    }

    // Query AD for fqdn to be used in check for group membership
    $sr = ldap_search($connect, $basedn, "(&(objectClass=user)(objectCategory=person)(|(sAMAccountName=$username)))");
    $info = ldap_get_entries($connect, $sr);
    $fullname=$info[0]["displayname"][0];
    $fqdn=$info[0]["dn"];

    $_SESSION["username"]=$username;
    $_SESSION["token"]=$password;
    $_SESSION["fullname"]=$fullname;
    $_SESSION["fqdn"]=$fqdn;

    // check to see if $admin_list or $user_list arrays are populated.
    // If arrays are populated check authenticated user for assigned role
    // otherwise assign all users the admin role by default
    if ((count($admin_list)>0) || (count($user_list)>0)) {
        for ($j=0; $j<count($user_list); $j++) {
            if (strtolower($username)==strtolower($user_list[$j])) {
                $_SESSION["role"]="user";
            } else {
                $sr=ldap_search($connect, $basedn, "(&(objectClass=group)(sAMAccountName=" . $user_list[$j] . "))");
                $info = ldap_get_entries($connect, $sr);
                for ($i=0; $i<$info["count"]; $i++) {
                    for ($k=0; $k<count($info[$i]["member"])-1; $k++) {
                        // Create SESSION variable if username is a member $user_list
                        if ($info[$i]["member"][$k] == $fqdn) {
                            $_SESSION["role"]="user";
                            break 3;
                        }
                    }
                }
            }
        }

        for ($j=0; $j<count($admin_list); $j++) {
            if ($username==$admin_list[$j]) {
                $_SESSION["role"]="admin";
            } else {
                $sr=ldap_search($connect, $basedn, "(&(objectClass=group)(sAMAccountName=" . $admin_list[$j] . "))");
                $info = ldap_get_entries($connect, $sr);
                for ($i=0; $i<$info["count"]; $i++) {
                    for ($k=0; $k<count($info[$i]["member"])-1; $k++) {
                        // Create SESSION variable if username is a member of $admin_list
                        if ($info[$i]["member"][$k] == $fqdn) {
                            $_SESSION["role"]="admin";
                            break 3;
                        }
                    }
                }
            }
        }
    } else {
            $_SESSION["role"]="admin";
    }

    if (isset($_SESSION["role"])) {
        // Close the connection
        ldap_unbind($connect);
        header('Location: '.$indexpage);
        exit;
    } else {
        session_destroy();
        ldap_unbind($connect);
        header('Location: '.$script);
        exit;
    }
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Open-AudIT Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">
<link rel="stylesheet" type="text/css" href="default.css" />
<!--[if lt IE 7]><link media="screen" rel="stylesheet" type="text/css" href="iefix.css" /><![endif]-->
</head>
<SCRIPT LANGUAGE="JavaScript">
       <!--
       document.onmousedown=click;
              function click()
                     {
                            if (event.button==2) {alert(<?php echo __("'Right-clicking has been disabled by the administrator.'");?>);}
                     }

//--></SCRIPT>
	<div class='npb_ldap_login_header'>
		<a href="index.php"><img src="images/logo.png"/></a>
	</div>

	<div class='npb_ldap_login'>
	<img src="/images/key.png"/>
	<h2 class='npb_ldap_login'>Please Login</h2>

	<form action="<? echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">

	<label><?php echo "Login Name:";?></label>
	<input TYPE="Text" name="username"><br />
	<label><?php echo __("Password:");?></label>
	<input TYPE="Password" name="password"><br />

	<input TYPE="Submit" id="submit" name="submit" value="Submit">

	</form>
</div>

<div class='npb_ldap_login_disclaimer'>
	<br /><?php echo __("This System is for the use of authorized users only.");?>
	<br /><?php echo __("Please login using your LDAP or Active Directory User Name and Password.");?>

<?php 
//
// Look for Result=false in the calling URI (actually just look for 'sult' cos we aren't that bothered ;} )
// This method seems to work regardless of register_globals, see http://uk2.php.net/manual/en/reserved.variables.php

if (@preg_match("/sult/i",$_SERVER['REQUEST_URI'])) {
// Warn them off if they screwed up the login.
echo '<br />'. __("Unauthorised use of this site may be a criminal offence.");
echo '<br />'.__(" Access attempt from ").' '.$_SERVER['REMOTE_ADDR'];
echo '<br />'.__("Your IP address and browser details will be logged.");
echo '<br />'. __("Any malicious attempt to access this site will be investigated.");
echo '<br />'. __("Please contact the administrator if you are having problems logging in.");

}else{
// Be gentle with them the first time.
//echo '<br><font face="Verdana,Tahoma,Arial,sans-serif" size="1" color="gray">'. __("Use of this site is subject to legal restrictions.").'</font>';
echo '<br />'. __("Use of this site is subject to legal restrictions.");
}
?>
</div>
</body>
</html>
<?php
die ();
}
?>
