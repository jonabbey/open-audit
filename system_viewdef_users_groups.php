<?php

$query_array=array("name"=>__("User & Groups"),
                   "image"=>"images/users_l.png",
                   "views"=>array("users"=>array(
                                                    "headline"=>__("Users"),
                                                    "sql"=>"SELECT * FROM users WHERE users_uuid = '".$_GET["pc"]."' AND users_timestamp = '".$GLOBAL["system_timestamp"]."' ",
                                                    "image"=>"./images/users_l.png",
                                                    "fields"=>array("10"=>array("name"=>"users_name",),
                                                                    "20"=>array("name"=>"users_full_name",),
                                                                    "30"=>array("name"=>"users_disabled",),
                                                                    "40"=>array("name"=>"users_password_changeable",),
                                                                    "50"=>array("name"=>"users_password_required",),
                                                                    "60"=>array("name"=>"ud_description",),
                                                                   ),
                                                    ),
                                   "groups"=>array(
                                                    "headline"=>__("Groups"),
                                                    "sql"=>"SELECT * FROM groups WHERE groups_uuid = '".$_GET["pc"]."' AND groups_timestamp = '".$GLOBAL["system_timestamp"]."' ORDER BY groups_name ",
                                                    "image"=>"images/groups_l.png",
                                                    "fields"=>array("10"=>array("name"=>"groups_name",),
                                                                    "20"=>array("name"=>"groups_members",),
                                                                    "30"=>array("name"=>"gd_description",),
                                                                    "40"=>array("name"=>"partition_boot_partition",),
                                                                   ),
                                                    ),
                                ),
                  );
?>
