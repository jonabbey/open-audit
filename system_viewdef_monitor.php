<?php

$query_array=array("name"=>__("Monitor"),
                   "views"=>array("summary"=>array(
                                                    "headline"=>__("Summary"),
                                                    "sql"=>"SELECT * FROM monitor WHERE monitor_id = '".$_REQUEST['monitor']."' ",
                                                    "image"=>"./images/o_x_terminal.png",
                                                    "edit"=>"y",
                                                    "fields"=>array("10"=>array("name"=>"monitor_model", "head"=>__("Model"), "edit"=>"n",),
                                                                    "20"=>array("name"=>"monitor_serial", "head"=>__("Serial"), "edit"=>"n",),
                                                                    "30"=>array("name"=>"monitor_uuid","head"=>__("Attached Device"),"edit"=>"n",),
                                                                    "40"=>array("name"=>"","head"=>__("")),
                                                                    "50"=>array("name"=>"monitor_first_timestamp", "head"=>__("Date First Audited"), "edit"=>"n",),
                                                                    "60"=>array("name"=>"monitor_timestamp", "head"=>__("Date Last Audited"), "edit"=>"n",),
                                                                    "70"=>array("name"=>"monitor_manufacturer", "head"=>__("Manufacturer"), "edit"=>"n",),
                                                                    "80"=>array("name"=>"monitor_manufacture_date", "head"=>__("Date of Manufacture"), "edit"=>"n",),
                                                                    "90"=>array("name"=>"monitor_date_purchased", "head"=>__("Date of Purchase"), "edit"=>"y",),
                                                                    "100"=>array("name"=>"monitor_purchase_order_number", "head"=>__("Purchase Order Number"), "edit"=>"y",),
                                                                    "110"=>array("name"=>"monitor_value", "head"=>__("Dollar Value"), "edit"=>"y",),
                                                                    "120"=>array("name"=>"monitor_description", "head"=>__("Description"), "edit"=>"y",),
                                                                   ),
                                                    ),
                                ),
                  );
?>
