<?php

$query_array=array("name"=>__("Monitor"),
                   "views"=>array("summary"=>array(
                                                    "headline"=>__("Summary"),
                                                    "sql"=>"SELECT * FROM monitor, system WHERE monitor_id = '".$_REQUEST['other']."' AND monitor_uuid = system_uuid",
                                                    "image"=>"./images/o_x terminal.png",
                                                    "fields"=>array("10"=>array("name"=>"monitor_model", "head"=>__("Model"),),
                                                                    "20"=>array("name"=>"system_name", "head"=>__("Attached Device"),),
                                                                    "30"=>array("name"=>"monitor_first_timestamp", "head"=>__("Date First Audited"),),
                                                                    "40"=>array("name"=>"monitor_manufacturer", "head"=>__("Manufacturer"),),
                                                                    "50"=>array("name"=>"monitor_serial", "head"=>__("Serial"),),
                                                                    "60"=>array("name"=>"monitor_manufacture_date", "head"=>__("Date of Manufacture"),),
                                                                    "70"=>array("name"=>"monitor_date_purchased", "head"=>__("Date of Purchase"),),
                                                                    "80"=>array("name"=>"monitor_purchase_order_number", "head"=>__("Purchase Order Number"),),
                                                                    "90"=>array("name"=>"monitor_value", "head"=>__("Dollar Value"),),
                                                                    "100"=>array("name"=>"monitor_description", "head"=>__("Description"),),
                                                                   ),
                                                    ),
                                ),
                  );
?>
