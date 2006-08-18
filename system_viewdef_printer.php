<?php

$query_array=array("name"=>__("Monitor"),
                   "views"=>array("summary"=>array(
                                                    "headline"=>__("Summary"),
                                                    "sql"=>"SELECT * FROM other WHERE other_type = 'printer' AND other_id = '".$_REQUEST["other"]."' ",
                                                    "image"=>"./images/printer_l.png",
                                                    "fields"=>array("10"=>array("name"=>"other_description", "head"=>__("Model"),),
                                                                    "20"=>array("name"=>"other_p_port_name", "head"=>__("Port"),),
                                                                    "30"=>array("name"=>"other_network_name", "head"=>__("Attached Device"),),
                                                                   ),
                                                    ),
                                ),
                  );
?>
