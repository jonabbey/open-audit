<?php

$query_array=array("headline"=>"IIS Virtual Servers on Host",
                   "sql"=>"SELECT * from iis_vd where iis_vd_uuid = '".$_REQUEST["pc"]."' AND iis_vd_timestamp = '".$GLOBALS["timestamp"]."' ",
                   "sort"=>"iis_vd_name",
                   "dir"=>"ASC",
                   "fields"=>array("10"=>array("name"=>"iis_vd_name",
                                               "head"=>"Name",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                   "20"=>array("name"=>"iis_vd_path",
                                               "head"=>"Path",
                                               "show"=>"y",
                                               "link"=>"n",
                                              ),
                                  ),
                  );
?>
