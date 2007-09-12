<?php
    session_start();
    if(!isset($_SESSION["role"]) and
       isset($use_ldap_login) and
       ($use_ldap_login == "y")
      )
    {
       header('Location: ldap_login.php');
       exit;
    }
    //echo "distinguishedName is: " . $_SESSION["fqdn"] . "<BR>";
    //echo "Username: " . $_SESSION["username"] . " Role: " . $_SESSION["role"] . "<BR>";
?>
