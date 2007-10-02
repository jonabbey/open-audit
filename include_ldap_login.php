<?php
session_start();
//put pages here that you wish to deny regular users access
$user_deny_pages = array("admin_config.php", "database_backup_form.php", "database_restore_form.php");
if(!isset($_SESSION["role"]) and isset($use_ldap_login) and ($use_ldap_login == "y")) {
    header('Location: ldap_login.php');
    exit;
} else {
    if ($_SESSION["role"]=="user") {
        if (array_search(basename($_SERVER['SCRIPT_NAME']), $user_deny_pages)!== False) {
            echo "Access Denied!";
            exit;
        }
    }  
//    //This section sets a session timout in seconds   
//    if (!isset($_SESSION["session_count"])) {
//        $_SESSION["session_count"]=0;
//        $_SESSION["session_start"]=time();
//    } else {
//        $_SESSION["session_count"]++;
//    } 
//    $session_timeout = 900; // 15 minutes (in sec)
//    $session_duration = (time() - $_SESSION["session_start"]);
//    if ($session_duration > $session_timeout) {
//        header("Location: ldap_logout.php");  // Redirect to Login Page
//    }
//    $_SESSION["session_start"]=time();
//    //End of session timeout section
}
?>