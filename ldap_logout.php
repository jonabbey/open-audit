<?php
        session_start();
        session_destroy();
        header('Location: ldap_login.php');
        exit;
?>
