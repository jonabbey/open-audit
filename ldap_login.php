<?php
        session_start();
        // Include LDAP settings from config file
        include "include_config.php";
        // Set variables to those defined in include_config.php
        $server = $ldap_server;
        $domain = $management_domain_suffix;
        $basedn = $ldap_base_dn;

        // When you view Active Directory with using LDAP, the Members attribute is not populated with the Primary group.
        // Domain Users is the primary group by default. The $admin_list and $user_list variables cannot contain the Primary group.

        // You can assign roles by populating these arrays with Active Directory user or group names enclosed in quotes
        // and separated separated by commas. If each is empty, all users are given the role of admin.
        //$admin_list = array("openauditadmin", "administrator");
        //$user_list = array("username1", "username2");

        $admin_list = array();
        $user_list = array();

        // Page to redirect to for initial logon or failed logon
        $script=$_SERVER['SCRIPT_NAME'];

        // Page to redirect to after successful authentication
        $page = "./index.php";

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
            $bind = ldap_bind($connect, $username_plus_upn, $password);
            if (!($bind) || ($username=="") || ($password=="")) {
                session_destroy();
                // Close the connection
                ldap_unbind($bind);
                header('Location: '.$script);
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
                    echo " MATCH USER USERNAME" . "<BR>";
                        $_SESSION["role"]="user";
                        } else {
                        $sr=ldap_search($connect, $basedn, "(&(objectClass=group)(sAMAccountName=" . $user_list[$j] . "))");
                        $info = ldap_get_entries($connect, $sr);
                        for ($i=0; $i<$info["count"]; $i++) {
                            for ($j=0; $j<count($info[$i]["member"])-1; $j++) {
                                // Create SESSION variable if username is a member $user_list
                                echo "Member " . $info[$i]["member"][$j] . "<BR>";
                                if ($info[$i]["member"][$j] == $fqdn) {
                                     echo " MATCH USER GROUP " . "<BR>";
                                     $_SESSION["role"]="user";
                                     break 3;
                                    }
                                }
                            }
                        }


                    }

                for ($j=0; $j<count($admin_list); $j++) {
                    if ($username==$admin_list[$j]) {
                        echo " MATCH ADMIN USERNAME" . "<BR>";
                        $_SESSION["role"]="admin";
                        } else {
                        $sr=ldap_search($connect, $basedn, "(&(objectClass=group)(sAMAccountName=" . $admin_list[$j] . "))");
                        $info = ldap_get_entries($connect, $sr);
                        for ($i=0; $i<$info["count"]; $i++) {
                            for ($j=0; $j<count($info[$i]["member"])-1; $j++) {
                                // Create SESSION variable if username is a member of $admin_list
                                echo "Member " . $info[$i]["member"][$j] . "<BR>";
                                if ($info[$i]["member"][$j] == $fqdn) {
                                   echo " MATCH ADMIN GROUP " . "<BR>";
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
                ldap_unbind($bind);
                header('Location: '.$page);
                exit;
                } else {
                session_destroy();
                ldap_unbind($bind);
                header('Location: '.$script);
                exit;
                }
        } else {
        ?>
        <html>
        <head>
        <title>Open-AudIT Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="pragma" content="no-cache">
        <link rel="stylesheet" type="text/css" href="default.css" />
        </head>
        <SCRIPT LANGUAGE="JavaScript">
                <!--
                        document.onmousedown=click;
                        function click()
                        {
                                if (event.button==2) {alert('Right-clicking has been disabled by the administrator.');}
                        }
                       
                //--></SCRIPT>
        <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
        <div align="center">
        <table width="50%" border="0" cellspacing="0" cellpadding="0">
        <table width="50%" border="0" cellspacing="0" cellpadding="0">
      <tr>
            <td colspan="1" ><a href="index.php"><img src="images/logo.png" width="300" height="48" alt="" style="border:0px;" /></a></td>
      </tr>
        <tr>
        <td align="center">
        <fieldset>
        <Legend><font face="Verdana,Tahoma,Arial,sans-serif" size="3" color="gray">Enter Credentials as username@domain.name</font></Legend>
        <table border="0" cellspacing="3" cellpadding="0">
        <tr>
        <td align="right" valign="middle"><b><font face="Verdana,Tahoma,Arial,sans-serif" size="1" color="gray">Username:</font></td>
        <td align="center" valign="middle">
        <input class="clear" type="text" size="15" name="username">
        </td>
        </tr>
        <tr>
        <td align="right" valign="middle"><b><font face="Verdana,Tahoma,Arial,sans-serif" size="1" color="gray">Password:</font></td>
        <td align="center" valign="middle">
        <input class="pass" type="password" size="15" name="password">
        </td>
        </tr>
        </table>
        <input type="submit" value="Submit">
        <br>
        </div>
        </td>
        </tr>
        </fieldset>
        </table>
        <br>
        <table width="640"><tr><td align="center">
        <font face="Verdana,Tahoma,Arial,sans-serif" size="1"
        color="gray">This System is for the use of authorized users only.</font>
        </td></tr><td align="center">
        <font face="Verdana,Tahoma,Arial,sans-serif" size="1"
        color="gray">Please login using your LDAP or Active Directory User Name and Password.</font>
        </td>
        </tr></table>
        </div>
        </form>
        </div>
        </body>
        </html>
        <?php
        die ();
        }
?>