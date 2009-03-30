<?php
/**********************************************************************************************************
Module:	include_ldap_config_err.php

Description:
	Displays warning that LDAP authentication is not correctly configured
		
Change Control:
	
	[Nick Brown]	02/03/2009
	Provides an error notification page where the system is configured to use LDAP authentication, but no LDAP connections have 
	been defined. In this case the user is logged on as admin anyway. This seemed preferable to locking admins out of the 
	system.
	
**********************************************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Open-AudIT Login</title>
<link rel="stylesheet" type="text/css" href="ldap_login.css" />
</head>
	<div class='npb_ldap_login_header'>
		<a href="index.php"><img src="images/logo.png"/></a>
	</div>

	<div class='npb_ldap_login'>
	<img src="images/Key.png"/>
	<h2 class='npb_ldap_login'>LDAP Config Error</h2>

	<form action="./index.php" method="POST">
		<p>You have enabled LDAP security but not defined any LDAP sources. 
		Please use the Admin -> Config menu option and select the LDAP page to define one or more LDAP connections.</p>
		<input TYPE="Submit" id="submit" name="submit" value="<?php echo __("Continue");?>">
	</form>
	
</div>

</body>
</html>
<?php
$_SESSION["role"] = "Admin";
$_SESSION["username"] = "Anonymous";
?>
