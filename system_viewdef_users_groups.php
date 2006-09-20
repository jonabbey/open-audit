<?php

$query_array=array("name"=>array("name"=>__("Users & Groups"),
                                 "sql"=>"SELECT `system_name` FROM `system` WHERE `system_uuid` = '" . $_GET["pc"] . "'",
                                ),
                   "image"=>"images/users_l.png",
                   "views"=>array("users"=>array(
                                                    "headline"=>__("Users"),
                                                    "sql"=>"SELECT * FROM users WHERE users_uuid = '".$_GET["pc"]."' AND users_timestamp = '".$GLOBAL["system_timestamp"]."' ",
                                                    "image"=>"./images/users_l.png",
                                                    "fields"=>array("10"=>array("name"=>"users_name", "head"=>__("Name"),),
                                                                    "20"=>array("name"=>"users_full_name", "head"=>__("Full Name"),),
                                                                    "30"=>array("name"=>"users_sid", "head"=>__("SID"),),
                                                                    "40"=>array("name"=>"users_disabled", "head"=>__("Disabled"),),
                                                                    "50"=>array("name"=>"users_password_changeable", "head"=>__("Password "),),
                                                                    "60"=>array("name"=>"users_password_required", "head"=>__("Changeable"),),
                                                                    "70"=>array("name"=>"ud_description", "head"=>__("Description"),),
                                                                   ),
                                                    ),
                                   "groups"=>array(
                                                    "headline"=>__("Groups"),
                                                    "sql"=>"SELECT * FROM groups WHERE groups_uuid = '".$_GET["pc"]."' AND groups_timestamp = '".$GLOBAL["system_timestamp"]."' ORDER BY groups_name ",
                                                    "image"=>"images/groups_l.png",
                                                    "fields"=>array("10"=>array("name"=>"groups_name", "head"=>__("Name"),),
                                                                    "20"=>array("name"=>"groups_members", "head"=>__("Members"),),
                                                                    "30"=>array("name"=>"gd_description", "head"=>__("Description"),),
                                                                   ),
                                                    ),
                                ),
                  );
?>
