<?php
    $_SESSION = array();
    session_destroy();
    ldap_unbind($bind);
    header('Location: '.$script);
    if(!isset($_SESSION["role"])and ($use_ldap_login = "y"))
    {
       header('Location: ldap_login.php');
       exit;
    }
    //echo "distinguishedName is: " . $_SESSION["fqdn"] . "<BR>";
    //echo "Username: " . $_SESSION["username"] . " Role: " . $_SESSION["role"] . "<BR>";
?>