<?php

$query_array=array("name"=>"Minitor",
                   "image"=>"images/monitor_l.png",
                   "views"=>array("summary"=>array(
                                                    "headline"=>"Monitor",
                                                    "sql"=>"SELECT * FROM monitor, system WHERE monitor_id = '" . $_GET['monitor'] . "' AND monitor_uuid = system_uuid ",
                                                    "image"=>"./images/summary_l.png",
                                                    "fields"=>array("10"=>array("name"=>"monitor_model",),
                                                                    "20"=>array("name"=>"system_name",),
                                                                    "30"=>array("name"=>"monitor_first_timestamp",),
                                                                    "40"=>array("name"=>"monitor_manufacturer",),
                                                                    "50"=>array("name"=>"monitor_serial",),
                                                                    "60"=>array("name"=>"monitor_manufacture_date",),
                                                                    "70"=>array("name"=>"monitor_date_purchased",),
                                                                    "80"=>array("name"=>"monitor_purchase_order_number",),
                                                                    "90"=>array("name"=>"monitor_value",),
                                                                    "100"=>array("name"=>"monitor_description",),
                                                                   ),
                                                    ),
                                ),
                  );
?>
